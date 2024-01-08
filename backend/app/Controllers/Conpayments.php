<?php

namespace App\Controllers;

use App\Models\Modelpayments;
use CodeIgniter\Files\File;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use PhpOffice\PhpSpreadsheet\IOFactory;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class Conpayments extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = model(Modelpayments::class);
    }

    public function index(): string
    {
        $data['message'] = UNAUTHORIZED_ACCESS;
        return view('errors/html/error_404.php', $data);
    }

    public function upload_pending()
    {

        try {

            $token = $this->request->getHeaderLine('x-token');
            $decoded = JWT::decode($token, new Key(env('SECRET_JWT'), 'HS256'));

            $userid = (int)$decoded->userid;

            $ticket = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);

            $file = $this->request->getFile('userfile');

            if (!$file->hasMoved()) {
                $data_upload = [];
                $filepath = WRITEPATH . 'uploads/payments/';
                $filename = $file->getName();

                $file->move($filepath, $filename);
                $ext = $file->getClientExtension();

                if ($ext != 'xlsx' && $ext != 'XLSX' && $ext != 'csv' && $ext != 'CSV') {
                    $resdata = array('type' => 'error', 'msg' => 'Solo es permitido (CSV, XLSX)');
                    return $this->response->setStatusCode(400)->setJSON($resdata);
                }

                if ($ext == 'xlsx' || $ext == 'XLSX') {
                    $spreadsheet = IOFactory::load($filepath . $filename);
                    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                    $i = 0;
                    foreach ($sheetData as $key) {
                        if ($i == 0) {
                            $i++;
                            continue;
                        }

                        $customer_document = $key['A'];
                        $customer_name = $key['B'];
                        $customer_email = $key['C'];
                        $amount = $key['D'];
                        $payment_date = $key['E'];
                        $due_date = $key['F'];

                        if (date('Y-m-d') > date("Y-m-d", strtotime($payment_date))) {
                            $resdata = array('type' => 'error', 'msg' => 'Revisar información hay pagos pendientes con fechas inferiores al día actual.');
                            return $this->response->setStatusCode(400)->setJSON($resdata);
                        }

                        if ($customer_document == '' || $customer_name == '' || $customer_email == '' || $amount == '' || $payment_date == '' || $due_date == '') {
                            $resdata = array('type' => 'error', 'msg' => 'Revisar información, todos los campos son requeridos.');
                            return $this->response->setStatusCode(400)->setJSON($resdata);
                        }

                        $data_upload[] = array(
                            'customer_document' => $customer_document,
                            'customer_name' => $customer_name,
                            'customer_email' => $customer_email,
                            'amount' => $amount,
                            'payment_date' => $payment_date,
                            'due_date' => $due_date,
                            'ticket' => $ticket,
                            'userid' => $userid,
                        );
                    }
                }

                if ($ext == 'csv' || $ext == 'CSV') {
                    $file_csv = new File($filepath . $filename);
                    $csv = $file_csv->openFile('r');

                    $i = 0;
                    while (($datos = $csv->fgetcsv(";")) !== false) {

                        if ($i == 0) {
                            $i++;
                            continue;
                        }

                        $customer_document = $datos['0'];
                        $customer_name = $datos['1'];
                        $customer_email = $datos['2'];
                        $amount = $datos['3'];
                        $payment_date = $datos['4'];
                        $due_date = $datos['5'];

                        if (date('Y-m-d') > date("Y-m-d", strtotime($payment_date))) {
                            $resdata = array('type' => 'error', 'msg' => 'Revisar información hay pagos pendientes con fechas inferiores al día actual.');
                            return $this->response->setStatusCode(400)->setJSON($resdata);
                        }

                        if ($customer_document == '' || $customer_name == '' || $customer_email == '' || $amount == '' || $payment_date == '' || $due_date == '') {
                            $resdata = array('type' => 'error', 'msg' => 'Revisar información, todos los campos son requeridos.');
                            return $this->response->setStatusCode(400)->setJSON($resdata);
                        }

                        $data_upload[] = array(
                            'customer_document' => $customer_document,
                            'customer_name' => $customer_name,
                            'customer_email' => $customer_email,
                            'amount' => $amount,
                            'payment_date' => $payment_date,
                            'due_date' => $due_date,
                            'ticket' => $ticket,
                            'userid' => $userid,
                        );
                    }
                }

                $data_res = $this->model->upload_pending($data_upload);

                if (!$data_res) {
                    $resdata = array('type' => 'error', 'msg' => 'Error al cargar archivo');
                    return $this->response->setStatusCode(400)->setJSON($resdata);
                }

                $resdata = array('type' => 'success', 'msg' => SUCCESS_REGISTER, 'ticket_upload' => $ticket);
                return $this->response->setStatusCode(200)->setJSON($resdata);
            }

            $resdata = array('type' => 'error', 'msg' => 'Error al cargar archivo');
            return $this->response->setStatusCode(400)->setJSON($resdata);
        } catch (\Throwable $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
            $resdata = array('type' => 'error', 'msg' => $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON($resdata);
        }
    }

    public function get_preview_upload_pending()
    {

        try {

            $ticket = (string)$this->request->getGet('ticket');

            $data = $this->model->get_preview_upload_pending($ticket);

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

    public function approved_upload_pendig()
    {
        try {
            $token = $this->request->getHeaderLine('x-token');
            $decoded = JWT::decode($token, new Key(env('SECRET_JWT'), 'HS256'));

            $userid = (int)$decoded->userid;

            $datajson = $this->request->getJSON();
            $ticket = $datajson->ticket;

            $data = $this->model->get_preview_upload_pending($ticket);

            if (!$data) {
                $resdata = array(
                    'type' => 'error',
                    'msg' => NOT_DATA
                );
                return $this->response->setStatusCode(400)->setJSON($resdata);
            }

            $data_approved = [];

            foreach ($data as $key) {

                $payment_id = $this->generateIdentifierPayment($key['payment_date'], $key['id_temp_upload_pending']);

                $data_approved[] = array(
                    'customer_document' => $key['customer_document'],
                    'customer_name' => $key['customer_name'],
                    'customer_email' => $key['customer_email'],
                    'amount' => $key['amount'],
                    'payment_date' => $key['payment_date'],
                    'due_date' => $key['due_date'],
                    'payment_id' => $payment_id,
                    'state_payment_id' => 1,
                    'userid' => $userid,
                );
            }

            $data_res = $this->model->approved_upload_pending($data_approved);

            if (!$data_res) {
                $resdata = array('type' => 'error', 'msg' => 'Error al aprobar carga');
                return $this->response->setStatusCode(400)->setJSON($resdata);
            }

            $this->model->clear_upload_pending($ticket);

            $resdata = array('type' => 'success', 'msg' => 'Se aprobó carga con exito');

            return $this->response->setStatusCode(200)->setJSON($resdata);
        } catch (\Throwable $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
            $resdata = array('type' => 'error', 'msg' => $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON($resdata);
        }
    }

    public function get_payments_state()
    {
        try {

            $date_start = (!empty($this->request->getGet('date_start'))) ? $this->request->getGet('date_start') : $this->dateFirstMonthDay();
            $date_end = (!empty($this->request->getGet('date_end'))) ? $this->request->getGet('date_end') : $this->dateLastMonthDay();
            $document = (empty($this->request->getGet('document'))) ? '' : "AND tup.customer_document='" . htmlentities((string)$this->request->getGet('document')) . "'";

            $data = $this->model->get_payments_state($date_start, $date_end, $document);

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

    public function upload_confirmation()
    {

        try {

            $token = $this->request->getHeaderLine('x-token');
            $decoded = JWT::decode($token, new Key(env('SECRET_JWT'), 'HS256'));

            $userid = (int)$decoded->userid;

            $ticket = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);

            $file = $this->request->getFile('userfile');

            if (!$file->hasMoved()) {
                $data_upload = [];
                $filepath = WRITEPATH . 'uploads/payments/';
                $filename = $file->getName();

                $file->move($filepath, $filename);
                $ext = $file->getClientExtension();

                if ($ext != 'xlsx' && $ext != 'XLSX' && $ext != 'csv' && $ext != 'CSV') {
                    $resdata = array('type' => 'error', 'msg' => 'Solo es permitido (CSV, XLSX)');
                    return $this->response->setStatusCode(400)->setJSON($resdata);
                }

                if ($ext == 'xlsx' || $ext == 'XLSX') {
                    $spreadsheet = IOFactory::load($filepath . $filename);
                    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                    $i = 0;
                    foreach ($sheetData as $key) {
                        if ($i == 0) {
                            $i++;
                            continue;
                        }

                        $customer_document = $key['A'];
                        $customer_name = $key['B'];
                        $customer_email = $key['C'];
                        $amount = $key['D'];
                        $payment_date = $key['E'];
                        $pyment_id = $key['F'];

                        $data_payment = $this->model->get_upload_payment_id($pyment_id);

                        if (date("Y-m-d", strtotime($payment_date)) > date("Y-m-d", strtotime($data_payment->due_date))) {
                            $resdata = array('type' => 'error', 'msg' => 'Revisar información hay pagos realizados fuera de rango de fecha de pago.');
                            return $this->response->setStatusCode(400)->setJSON($resdata);
                        }

                        if ($customer_document == '' || $customer_name == '' || $customer_email == '' || $amount == '' || $payment_date == '' || $pyment_id == '') {
                            $resdata = array('type' => 'error', 'msg' => 'Revisar información, todos los campos son requeridos.');
                            return $this->response->setStatusCode(400)->setJSON($resdata);
                        }

                        if ($amount != $data_payment->amount) {
                            $resdata = array('type' => 'error', 'msg' => 'Revisar información, hay valores que no son iguales a los registrados en pagos pendientes.');
                            return $this->response->setStatusCode(400)->setJSON($resdata);
                        }

                        $data_upload[] = array(
                            'customer_document' => $customer_document,
                            'customer_name' => $customer_name,
                            'customer_email' => $customer_email,
                            'amount' => $amount,
                            'payment_date' => $payment_date,
                            'pyment_id' => $pyment_id,
                            'ticket' => $ticket,
                            'userid' => $userid,
                        );
                    }
                }

                if ($ext == 'csv' || $ext == 'CSV') {
                    $file_csv = new File($filepath . $filename);
                    $csv = $file_csv->openFile('r');

                    $i = 0;
                    while (($datos = $csv->fgetcsv(";")) !== false) {

                        if ($i == 0) {
                            $i++;
                            continue;
                        }

                        $customer_document = $datos['0'];
                        $customer_name = $datos['1'];
                        $customer_email = $datos['2'];
                        $amount = $datos['3'];
                        $payment_date = $datos['4'];
                        $pyment_id = $datos['5'];

                        $data_payment = $this->model->get_upload_payment_id($pyment_id);

                        if (date("Y-m-d", strtotime($payment_date)) > date("Y-m-d", strtotime($data_payment->due_date))) {
                            $resdata = array('type' => 'error', 'msg' => 'Revisar información hay pagos realizados fuera de rango de fecha de pago.');
                            return $this->response->setStatusCode(400)->setJSON($resdata);
                        }

                        if ($customer_document == '' || $customer_name == '' || $customer_email == '' || $amount == '' || $payment_date == '' || $pyment_id == '') {
                            $resdata = array('type' => 'error', 'msg' => 'Revisar información, todos los campos son requeridos.');
                            return $this->response->setStatusCode(400)->setJSON($resdata);
                        }

                        if ($amount != $data_payment->amount) {
                            $resdata = array('type' => 'error', 'msg' => 'Revisar información, hay valores que no son iguales a los registrados en pagos pendientes.');
                            return $this->response->setStatusCode(400)->setJSON($resdata);
                        }

                        $data_upload[] = array(
                            'customer_document' => $customer_document,
                            'customer_name' => $customer_name,
                            'customer_email' => $customer_email,
                            'amount' => $amount,
                            'payment_date' => $payment_date,
                            'pyment_id' => $pyment_id,
                            'ticket' => $ticket,
                            'userid' => $userid,
                        );
                    }
                }

                $data_res = $this->model->upload_confirmation($data_upload);

                if (!$data_res) {
                    $resdata = array('type' => 'error', 'msg' => 'Error al cargar archivo');
                    return $this->response->setStatusCode(400)->setJSON($resdata);
                }

                $resdata = array('type' => 'success', 'msg' => SUCCESS_REGISTER, 'ticket_upload' => $ticket);
                return $this->response->setStatusCode(200)->setJSON($resdata);
            }

            $resdata = array('type' => 'error', 'msg' => 'Error al cargar archivo');
            return $this->response->setStatusCode(400)->setJSON($resdata);
        } catch (\Throwable $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
            $resdata = array('type' => 'error', 'msg' => $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON($resdata);
        }
    }

    public function get_preview_upload_confirmation()
    {

        try {

            $ticket = (string)$this->request->getGet('ticket');

            $data = $this->model->get_preview_upload_confirmation($ticket);

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

    public function approved_upload_confirmation()
    {
        try {
            $token = $this->request->getHeaderLine('x-token');
            $decoded = JWT::decode($token, new Key(env('SECRET_JWT'), 'HS256'));

            $userid = (int)$decoded->userid;

            $datajson = $this->request->getJSON();
            $ticket = $datajson->ticket;

            $data = $this->model->get_preview_upload_confirmation($ticket);

            if (!$data) {
                $resdata = array(
                    'type' => 'error',
                    'msg' => NOT_DATA
                );
                return $this->response->setStatusCode(400)->setJSON($resdata);
            }

            foreach ($data as $key) {
                $data_res = $this->model->approved_upload_confirmation($key['payment_id'], $userid);
                if (!$data_res) {
                    $resdata = array('type' => 'error', 'msg' => 'Error al aprobar carga');
                    return $this->response->setStatusCode(400)->setJSON($resdata);
                }
            }

            $this->model->clear_upload_confirmation($ticket);

            $resdata = array('type' => 'success', 'msg' => 'Se aprobó carga con exito');

            return $this->response->setStatusCode(200)->setJSON($resdata);
        } catch (\Throwable $e) {
            log_message('error', ERROR_EXCEPTION, ['exception' => $e]);
            $resdata = array('type' => 'error', 'msg' => $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON($resdata);
        }
    }

    private function generateIdentifierPayment($payment_date, $id_payment_pending)
    {
        $length_id = 20;
        $date_format = date("Ymd", strtotime($payment_date));
        $length_payment_date = strlen($date_format);
        $length_fill_zero = $length_id - $length_payment_date;
        $fill_zero = str_pad($id_payment_pending, $length_fill_zero, '0', STR_PAD_LEFT);
        return $date_format . $fill_zero;
    }

    private function dateLastMonthDay()
    {
        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));
        return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
    }

    private function dateFirstMonthDay()
    {
        $month = date('m');
        $year = date('Y');
        return date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
    }
}
