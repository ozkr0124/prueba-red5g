<?php

namespace App\Models;

use CodeIgniter\Model;

class Modelusers extends Model
{

    public function register(
        String $full_name,
        String $document_id,
        String $email,
        String $user_name,
        String $password,
        int $state,
        int $role_id
    ): mixed
    {
        try {
            $sql = "INSERT INTO tbl_users (full_name, document_id, email, user_name, password, state, role_id) VALUES
            (:full_name:, :document_id:, :email:, :user_name:, :password:, :state:, :role_id:);";
            $this->db->query($sql, [
                'full_name' => $full_name,
                'document_id' => $document_id,
                'email' => $email,
                'user_name' => $user_name,
                'password' => $password,
                'state' => $state,
                'role_id' => $role_id,
            ]);
            return ($this->db->affectedRows() > 0) ? 1 : 0;
        } catch (\Exception $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
        }
    }

    public function get_users(): mixed
    {
        try {
            $sql = "SELECT u.full_name, u.document_id, u.email, u.user_name, u.state, u.role_id, r.role
            FROM tbl_users u
            INNER JOIN tbl_roles r ON r.id_roles = u.role_id;";
            $query = $this->db->query($sql);
            $resQuery = $query->getResultArray();
            return ($query->getNumRows() > 0) ? $resQuery : 0;
        } catch (\Exception $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
        }
    }
}
