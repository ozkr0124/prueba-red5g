<?php

namespace App\Controllers;

use App\Models\Modelauthentication;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Conauthentication extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = model(Modelauthentication::class);
    }

    public function index(): string
    {
        $data['message'] = UNAUTHORIZED_ACCESS;
        return view('errors/html/error_404.php', $data);
    }

    public function login()
    {

        try {

            $datajson = $this->request->getJSON();

            $user_name = $datajson->user_name;
            $password = $datajson->password;

            $data = $this->model->login($user_name);

            if (!$data) {
                $resdata = array(
                    'type' => 'error',
                    'msg' => ERROR_AUTH
                );
                return $this->response->setStatusCode(400)->setJSON($resdata);
            }

            $passwordHash = $data->password;

            if (!password_verify($password, $passwordHash)) {
                $resdata = array(
                    'type' => 'error',
                    'msg' => ERROR_AUTH
                );
                return $this->response->setStatusCode(400)->setJSON($resdata);
            }

            $userstate = $data->state;

            if (!$userstate) {
                $resdata = array(
                    'type' => 'error',
                    'msg' => ERROR_AUTH
                );
                return $this->response->setStatusCode(400)->setJSON($resdata);
            }

            $payload = [
                'iss' => env('app.baseURL'),
                'sub' => 'pruebaRed5g',
                'exp' => time() + 36000,
                'iat' => strtotime(date('Y-m-d H:i:s')),
                'userid' => $data->id_users,
                'email' => $data->email,
                'user_name' => $data->user_name,
                'documentid' => $data->document_id,
                'roleid' => $data->role_id,
            ];

            unset($data->password);
            unset($data->created_at);
            unset($data->updated_at);
            unset($data->state);

            $jwt = JWT::encode($payload, env('SECRET_JWT'), 'HS256');

            $resdata = array('type' => 'success', 'msg' => $data, 'token' => $jwt);

            return $this->response->setStatusCode(200)->setJSON($resdata);
        } catch (\Throwable $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
            $resdata = array('type' => 'error', 'msg' => $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON($resdata);
        }
    }
}
