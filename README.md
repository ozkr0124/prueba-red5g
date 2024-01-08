# PRUEBA FULL STACK RED 5G

## Backend

Implementación de backend con lenguaje PHP y framework Codeigniter 4.

Versiones
- PHP 8.2.10
- Codeigniter v4.4.4
- MariaDB 11.1.2

## Frontend

Implementación de frontend con HTML - CSS - JavaScript y framework jQuery

Versiones
- jQuery 3.7.1
<br>
<br>
<br>

# Como usar metodo Docker

En la carpeta raiz se encuentra el archivo **docker-compose.yml** el cual permite inicair los contenedores tanto de backend como de frontend.

Ejecutar el siguiente comando para iniciar

```sh
docker compose up -d
```

Una vez inicializado por completo los contenedores se debe de ingresar al contenedor con el nombre **php-fpm-prueba** y ejecutar el siguiente comando para poder instalar los paquetes compose

```sh
composer install
```

Una vez se haya finalizado la inicialización de los contenedores ya se puede ingresar con el siguiente link al frontend


```sh
http://localhost:8090/login.html
```

# Como usar metodo local

Se debe de cargar las carpetas de backend y frontend en la carpeta raiz del servidor web.

En el archivo ***script.js*** que se encuentra dentro de la carpeta de ***frontend/assets/js/*** se debe de modificar la **base_url** para que apunte al servidor nuevo que es el backend

```js
const base_url = 'http://[IP ADDRESS O HOST API]/';
```

En la raiz del repositorio se encuentra el archivo ***schema.sql*** el cual se debe de cargar en el servidor de base de datos ***MySQL*** para cargar la estructura inicial del proyecto