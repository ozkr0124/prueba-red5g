<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data['message'] = UNAUTHORIZED_ACCESS;
        return view('errors/html/error_404.php', $data);
    }

    public function error_jwt()
    {
        $resdata = array('type' => 'error_token', 'msg' => 'Token invalid');
        return $this->response->setStatusCode(400)->setJSON($resdata);
    }
}
