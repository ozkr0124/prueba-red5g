const base_url = 'http://localhost:8099/';

let sleep = function(ms){
    return new Promise(resolve => setTimeout(resolve, ms));
};

function showPassword() {
    var tipo = document.getElementById("password");
    var icon = document.getElementById("iconVis");
    if (tipo.type == "password") {
        tipo.type = "text";
        icon.innerHTML = 'visibility_off';
    } else {
        tipo.type = "password";
        icon.innerHTML = 'visibility';
    }
}

async function login() {

    let username = $('#username').val();
    let password = $('#password').val();

    if (username == '' || password == '') {
        Swal.fire({
            title: "Error",
            text: "Usuario y/o contraseño incorrecto.",
            icon: "error"
        });
        return false;
    }

    let data = {
        "user_name": username,
        "password": password
    }

    let options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    }

    let fetchRes = await fetch(base_url + 'login', options).catch(error => console.error(error));
    let resdata = await fetchRes.json();

    if (resdata.type == 'error_token') {
        await logout();
        return false;
    }

    if (resdata.type == 'error') {
        Swal.fire({
            title: "Error",
            text: resdata.msg,
            icon: "error"
        });
        return false;
    }

    window.sessionStorage.setItem('token', resdata.token);

    for (const key in resdata.msg) {
        if (Object.hasOwnProperty.call(resdata.msg, key)) {
            const element = resdata.msg[key];
            window.sessionStorage.setItem(key, element);
        }
    }

    window.location.href = './dashboard.html';

}

async function logout() {
    window.sessionStorage.clear();
    window.location.href = './login.html';
}

async function router_view(view) {
    window.location.href = `./${view}.html`;
}

async function upload_file_payment(type) {

    try {
        $('.container-loader').removeClass("hidden");
        $('.container').addClass("hidden");

        await sleep(3000);

        let route = {
            'pending': 'upload-pending',
            'confirmation': 'upload-confirmation',
        }

        let formData = new FormData();
        let fileField = document.querySelector("input[type='file']");

        formData.append('userfile', fileField.files[0]);

        let options = {
            method: 'POST',
            headers: {
                'x-token': window.sessionStorage.getItem('token'),
            },
            body: formData
        }

        let fetchRes = await fetch(`${base_url}/${route[type]}`, options).catch(error => console.error(error));
        let resdata = await fetchRes.json();

        if (resdata.type == 'error_token') {
            await logout();
            return false;
        }

        if (resdata.type == 'error') {

            $('.container-loader').addClass("hidden");
            $('.container').removeClass("hidden");

            await Swal.fire({
                title: "Error",
                text: resdata.msg,
                icon: "error"
            });
            return false;
        }

        if (resdata.type == 'success') {
            window.location.href = `./preview-upload.html?ticket=${resdata.ticket_upload}&type=${type}`;
        }
    } catch (error) {
        console.log('Error:', error);
        Swal.fire({
            title: "Error",
            icon: "error"
        });
        return false;
    }


}

async function approved_upload() {

    try {
        const paramsString = location.search;
        const queryParams = new URLSearchParams(paramsString);
        const ticket = queryParams.get('ticket');
        const type = queryParams.get('type');

        let data = {
            "ticket": ticket,
        }

        let options = {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'x-token': window.sessionStorage.getItem('token'),
            },
            body: JSON.stringify(data)
        }

        let pathUrl = (type == 'pending') ? `approved-upload-pendig` : `approved-upload-confirmation`;

        let fetchRes = await fetch(base_url + pathUrl, options).catch(error => console.error(error));
        let resdata = await fetchRes.json();

        if (resdata.type == 'error_token') {
            await logout();
            return false;
        }

        Swal.fire({
            title: resdata.type.charAt(0).toUpperCase() + resdata.type.slice(1),
            text: resdata.msg,
            icon: resdata.type
        }).then(async (result) => {
            if (result.isConfirmed) {
                await router_view('dashboard');
            }
        });

    } catch (error) {
        console.log('Error:', error);
        Swal.fire({
            title: "Error",
            icon: "error"
        });
        return false;
    }

}

async function register_user() {
    try {
        let full_name = $('#full_name').val();
        let document_id = $('#document_id').val();
        let email = $('#email').val();
        let user_name = $('#user_name').val();
        let password = $('#password').val();
        let repassword = $('#repassword').val();

        if (full_name == '' || document_id == '' || email == '' || user_name == '' || password == '') {
            Swal.fire({
                title: "Error",
                text: "Todos los campos son requeridos.",
                icon: "error"
            });
            return false;
        }

        if (password !== repassword) {
            Swal.fire({
                title: "Error",
                text: "El password y confirmar password no son iguales.",
                icon: "error"
            });
            return false;
        }

        let data = {
            "full_name": full_name,
            "document_id": document_id,
            "email": email,
            "user_name": user_name,
            "password": password,
            "state": 1,
            "role_id": 2
        }
        
        let options = {
           method: 'POST',
           headers: {
               'Content-Type': 'application/json',
               'x-token': window.sessionStorage.getItem('token'),
           },
           body: JSON.stringify(data)
        }
        
        let fetchRes = await fetch(base_url + 'register', options).catch(error => console.error(error));
        let resdata = await fetchRes.json();

        Swal.fire({
            title: resdata.type.charAt(0).toUpperCase() + resdata.type.slice(1),
            text: resdata.msg,
            icon: resdata.type
        }).then(async (result) => {
            if (result.isConfirmed) {
                await router_view('users');
            }
        });
        
    } catch (error) {

        console.log('Error:', error);
        Swal.fire({
            title: "Error",
            icon: "error"
        });
        return false;
        
    }
}