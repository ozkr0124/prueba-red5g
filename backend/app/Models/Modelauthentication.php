<?php

namespace App\Models;

use CodeIgniter\Model;

class Modelauthentication extends Model
{

    public function login(String $username): mixed
    {
        try {
            $sql = "SELECT u.*, r.role
            FROM tbl_users u
            INNER JOIN tbl_roles r ON r.id_roles = u.role_id
            WHERE user_name = :username:;";
            $query = $this->db->query($sql, [
                'username' => $username
            ]);
            $resQuery = $query->getRow();
            return ($query->getNumRows() > 0) ? $resQuery : 0;
        } catch (\Exception $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
        }
    }
}
