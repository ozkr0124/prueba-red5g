<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//$routes->get('/', 'Home::index');
$routes->get('/error_jwt', 'Home::error_jwt');

$routes->get('/', 'Conauthentication::index');
$routes->post('/login', 'Conauthentication::login');

$routes->post('/register', 'Conusers::register', ['filter' => 'filterauth']);
$routes->get('/users', 'Conusers::get_users', ['filter' => 'filterauth']);

$routes->post('/upload-pending', 'Conpayments::upload_pending', ['filter' => 'filterauth']);
$routes->get('/preview-upload-pending', 'Conpayments::get_preview_upload_pending', ['filter' => 'filterauth']);
$routes->put('/approved-upload-pendig', 'Conpayments::approved_upload_pendig', ['filter' => 'filterauth']);
$routes->get('/payments-state', 'Conpayments::get_payments_state', ['filter' => 'filterauth']);
$routes->post('/upload-confirmation', 'Conpayments::upload_confirmation', ['filter' => 'filterauth']);
$routes->get('/preview-upload-confirmation', 'Conpayments::get_preview_upload_confirmation', ['filter' => 'filterauth']);
$routes->put('/approved-upload-confirmation', 'Conpayments::approved_upload_confirmation', ['filter' => 'filterauth']);

