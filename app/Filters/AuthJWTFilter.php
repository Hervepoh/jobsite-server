<?php

namespace App\Filters;

use App\Services\SessionService;
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
            $userId = $decoded->userId;
            $sessionId = $decoded->sessionId;

            if (!$userId || !$sessionId) {
                return service('response')->setStatusCode(401)->setJSON(['error' => 'You need to login to perform this action']);
            }

            $session =  (new SessionService())->getSessionById($decoded->sessionId);
            if (!$session) {
                return service('response')->setStatusCode(401)->setJSON(['error' => 'You need to login to perform this action']);
            }

            // Injection de sessionId dans la requete 
            $request->sessionId = $sessionId;
            $request->userId = $userId;
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
