<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthJWTFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $token = $_COOKIE['accessToken'] ?? null;

        if (!$token) {
            return service('response')->setStatusCode(401)->setJSON(['error' => 'You need to login to perform this action']);
        }

        try {
            helper('\App\Helpers\Jwt');
            $decoded = decode_jwt($token);

            if (!$decoded->userId || !$decoded->sessionId) {
                return service('response')->setStatusCode(401)->setJSON(['error' => 'You need to login to perform this action']);
            }
            

            // Injection de sessionId dans la requete 
            $request->sessionId = $decoded->sessionId ?? null;
            $request->userId = $decoded->userId ?? null;
        } catch (\Exception $e) {
            return service('response')->setStatusCode(401)->setJSON(['error' => 'Invalid token']);
        }

        return null; // Autorisé
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return $response;
    }
}
