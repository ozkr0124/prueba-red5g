<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

use Firebase\JWT\Key;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use DomainException;
use InvalidArgumentException;
use UnexpectedValueException;

class Filterauth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        try {
            $token = $request->getHeaderLine('x-token'); // Aca se debe de capturar el token en el header
            JWT::decode($token, new Key(env('SECRET_JWT'), 'HS256'));
        } catch (InvalidArgumentException $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            return redirect()->to(site_url('error_jwt'));
            // provided key/key-array is empty or malformed.
        } catch (DomainException $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            return redirect()->to(site_url('error_jwt'));
            // provided algorithm is unsupported OR
            // provided key is invalid OR
            // unknown error thrown in openSSL or libsodium OR
            // libsodium is required but not available.
        } catch (SignatureInvalidException $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            return redirect()->to(site_url('error_jwt'));
            // provided JWT signature verification failed.
        } catch (BeforeValidException $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            return redirect()->to(site_url('error_jwt'));
            // provided JWT is trying to be used before "nbf" claim OR
            // provided JWT is trying to be used before "iat" claim.
        } catch (ExpiredException $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            return redirect()->to(site_url('error_jwt'));
            // provided JWT is trying to be used after "exp" claim.
        } catch (UnexpectedValueException $e) {
            log_message('error', '[ERROR] {exception}', ['exception' => $e]);
            return redirect()->to(site_url('error_jwt'));
            // provided JWT is malformed OR
            // provided JWT is missing an algorithm / using an unsupported algorithm OR
            // provided JWT algorithm does not match provided key OR
            // provided key ID in key/key-array is empty or invalid.
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
