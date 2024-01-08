<?php

namespace App\Controllers;

use App\Models\Modelusers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Conusers extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = model(Modelusers::class);
    }

    public function index(): string
    {
        $data['message'] = UNAUTHORIZED_ACCESS;
        return view('errors/html/error_404.php', $data);
    }

    public function register()
    {

        try {

            $token = $this->request->getHeaderLine('x-token');
            $decoded = JWT::decode($token, new Key(env('SECRET_JWT'), 'HS256'));

            $roleid = (int)$decoded->roleid;

            if ($roleid !== 1) {
                $resdata = array(
                    'type' => 'error',
                    'msg' => UNAUTHORIZED,
                );
                return $this->response->setStatusCode(400)->setJSON($resdata);
            }

            $datajson = $this->request->getJSON();

            foreach ($datajson as $key => $value) {
                if ($value == '') {
                    $resdata = array(
                        'type' => 'error',
                        'msg' => ERROR_FIELDS_REQUIRED,
                        'field' => $key
                    );
                    return $this->response->setStatusCode(400)->setJSON($resdata);
                }
            }

            $full_name = $datajson->full_name;
            $document_id = (int)$datajson->document_id;
            $email = $datajson->email;
            $user_name = $datajson->user_name;
            $password = $datajson->password;
            $state = (int)$datajson->state;
            $role_id = (int)$datajson->role_id;

            $password_hash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);

            $data = $this->model->register(
                $full_name,
                $document_id,
                $email,
                $user_name,
                $password_hash,
                $state,
                $role_id
            );

            if (!$data) {
                $resdata = array(
                    'type' => 'error',
                    'msg' => ERROR_REGISTER
                );
                return $this->response->setStatusCode(400)->setJSON($resdata);
            }

            $resdata = array('type' => 'success', 'msg' => SUCCESS_REGISTER);

            return $this->response->setStatusCode(200)->setJSON($resdata);
        } catch (\Throwable $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
            $resdata = array('type' => 'error', 'msg' => $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON($resdata);
        }
    }

    public function get_users() {

        try {

            $data = $this->model->get_users();

            if (!$data) {
                $resdata = array(
                    'type' => 'error',
                    'msg' => NOT_DATA
                );
                return $this->response->setStatusCode(400)->setJSON($resdata);
            }

            $resdata = array('type' => 'success', 'msg' => $data);

            return $this->response->setStatusCode(200)->setJSON($resdata);
        } catch (\Throwable $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
            $resdata = array('type' => 'error', 'msg' => $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON($resdata);
        }
        
    }
}
