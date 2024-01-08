<?php

namespace App\Models;

use CodeIgniter\Model;

class Modelpayments extends Model
{
    public function upload_pending(array $data): mixed
    {
        try {
            $sql_header = "INSERT INTO tbl_temp_upload_pending
            (customer_document, customer_name, customer_email, amount, payment_date, due_date, ticket_upload, user_id) VALUES";
            $sql_values = "";
            foreach ($data as $key) {
                $sql_values .= "(" . $this->db->escape($key['customer_document']) . ", " . $this->db->escape($key['customer_name']) . ", " . $this->db->escape($key['customer_email']) . ", " . $this->db->escape($key['amount']) . ", " . $this->db->escape($key['payment_date']) . ", " . $this->db->escape($key['due_date']) . ", " . $this->db->escape($key['ticket']) . ", " . $this->db->escape($key['userid']) . "),";
            }
            $sql_values = trim($sql_values, ',');
            $sql = $sql_header . $sql_values . ";";
            $this->db->query($sql);
            return ($this->db->affectedRows() > 0) ? 1 : 0;
        } catch (\Exception $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
        }
    }

    public function get_preview_upload_pending(String $ticket): mixed
    {
        try {
            $sql = "SELECT id_temp_upload_pending, customer_document, customer_name, customer_email, amount, payment_date, due_date, ticket_upload, user_id
            FROM tbl_temp_upload_pending
            WHERE ticket_upload = :ticket:;";
            $query = $this->db->query($sql, [
                'ticket' => $ticket
            ]);
            $resQuery = $query->getResultArray();
            return ($query->getNumRows() > 0) ? $resQuery : 0;
        } catch (\Exception $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
        }
    }

    public function approved_upload_pending(array $data): mixed
    {
        try {
            $sql_header = "INSERT INTO tbl_upload_payments
            (customer_document, customer_name, customer_email, amount, payment_date, due_date, payment_id, state_payment_id, user_id) VALUES";
            $sql_values = "";
            foreach ($data as $key) {
                $sql_values .= "(" . $this->db->escape($key['customer_document']) . ", " . $this->db->escape($key['customer_name']) . ", " . $this->db->escape($key['customer_email']) . ", " . $this->db->escape($key['amount']) . ", " . $this->db->escape($key['payment_date']) . ", " . $this->db->escape($key['due_date']) . ", " . $this->db->escape($key['payment_id']) . ", " . $this->db->escape($key['state_payment_id']) . ", " . $this->db->escape($key['userid']) . "),";
            }
            $sql_values = trim($sql_values, ',');
            $sql = $sql_header . $sql_values . ";";
            $this->db->query($sql);
            return ($this->db->affectedRows() > 0) ? 1 : 0;
        } catch (\Exception $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
        }
    }

    public function clear_upload_pending(String $ticket): mixed
    {
        try {
            $sql = "DELETE FROM tbl_temp_upload_pending WHERE ticket_upload = :ticket:;";
            $this->db->query($sql, [
                'ticket' => $ticket
            ]);
            return ($this->db->affectedRows() > 0) ? 1 : 0;
        } catch (\Exception $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
        }
    }

    public function get_payments_state(String $date_start, String $date_end, String $document): mixed
    {
        try {
            $sql = "SELECT tup.id_upload_payments, tup.customer_document, tup.customer_name, tup.customer_email, tup.amount, tup.payment_date, tup.due_date, tup.payment_id, tup.state_payment_id, tsp.state_payment, tup.user_id, tup.created_at, tup.updated_at
            FROM tbl_upload_payments tup
            INNER JOIN tbl_state_payments tsp ON tsp.id_state_payments = tup.state_payment_id
            WHERE tup.payment_date BETWEEN :date_start: AND :date_end: $document;";
            $query = $this->db->query($sql, [
                'date_start' => $date_start,
                'date_end' => $date_end,
            ]);
            $resQuery = $query->getResultArray();
            return ($query->getNumRows() > 0) ? $resQuery : 0;
        } catch (\Exception $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
        }
    }

    public function get_upload_payment_id(String $pyment_id): mixed
    {
        try {
            $sql = "SELECT tup.id_upload_payments, tup.customer_document, tup.customer_name, tup.customer_email, tup.amount, tup.payment_date, tup.due_date, tup.payment_id, tup.state_payment_id, tsp.state_payment, tup.user_id, tup.created_at, tup.updated_at
            FROM tbl_upload_payments tup
            INNER JOIN tbl_state_payments tsp ON tsp.id_state_payments = tup.state_payment_id
            WHERE tup.payment_id = :pyment_id:";
            $query = $this->db->query($sql, [
                'pyment_id' => $pyment_id,
            ]);
            $resQuery = $query->getRow();
            return ($query->getNumRows() > 0) ? $resQuery : 0;
        } catch (\Exception $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
        }
    }

    public function upload_confirmation(array $data): mixed
    {
        try {

            $sql_header = "INSERT INTO tbl_temp_upload_confirmation (customer_document, customer_name, customer_email, amount, payment_date, payment_id, ticket_upload, user_id) VALUES";
            $sql_values = "";
            foreach ($data as $key) {
                $sql_values .= "(" . $this->db->escape($key['customer_document']) . ", "
                    . $this->db->escape($key['customer_name']) . ","
                    . $this->db->escape($key['customer_email']) . ","
                    . $this->db->escape($key['amount']) . ","
                    . $this->db->escape($key['payment_date']) . ","
                    . $this->db->escape($key['pyment_id']) . ","
                    . $this->db->escape($key['ticket']) . ","
                    . $this->db->escape($key['userid']) . "),";
            }

            $sql_values = trim($sql_values, ',');
            $sql = $sql_header . $sql_values . ";";

            $this->db->query($sql);
            return ($this->db->affectedRows() > 0) ? 1 : 0;
        } catch (\Exception $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
        }
    }

    public function get_preview_upload_confirmation(String $ticket): mixed
    {
        try {
            $sql = "SELECT id_temp_upload_confirmation, customer_document, customer_name, customer_email, amount, payment_date, payment_id, ticket_upload, user_id
            FROM tbl_temp_upload_confirmation
            WHERE ticket_upload = :ticket:;";
            $query = $this->db->query($sql, [
                'ticket' => $ticket
            ]);
            $resQuery = $query->getResultArray();
            return ($query->getNumRows() > 0) ? $resQuery : 0;
        } catch (\Exception $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
        }
    }

    public function approved_upload_confirmation(String $payment_id, int $user_id): mixed
    {
        try {
            $sql = "UPDATE tbl_upload_payments SET state_payment_id = 2, user_id_approved = :user_id_approved: WHERE payment_id = :payment_id:;";
            $this->db->query($sql, [
                'user_id_approved' => $user_id,
                'payment_id' => $payment_id
            ]);
            return ($this->db->affectedRows() > 0) ? 1 : 0;
        } catch (\Exception $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
        }
    }

    public function clear_upload_confirmation(String $ticket): mixed
    {
        try {
            $sql = "DELETE FROM tbl_temp_upload_confirmation WHERE ticket_upload = :ticket:;";
            $this->db->query($sql, [
                'ticket' => $ticket
            ]);
            return ($this->db->affectedRows() > 0) ? 1 : 0;
        } catch (\Exception $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
        }
    }
}
