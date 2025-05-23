<?php

namespace App\Controllers\Api;

use App\Models\SessionModel;
use App\Models\UserModel;
use App\Services\SessionService;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use stdClass;

class SessionController extends ResourceController
{
    protected $format = 'json';

    protected SessionService $sessionService;

    public function __construct()
    {
        $this->sessionService = new SessionService();
    }

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function getAllSessions(): ResponseInterface
    {
        $userId = $this->request->userId ?? null;
        $currentSessionId = $this->request->sessionId ?? null;

        if (!$userId || !$currentSessionId) {
            return $this->failUnauthorized('User not authenticated.');
        }

        $sessions = $this->sessionService->getAllSessions($userId);
 
        $modifiedSessions = array_map(function ($session) use ($currentSessionId) {
            return array_merge(
                (array) $session,
                ['isCurrent' => $session->id === $currentSessionId]
            );
        }, $sessions);

        return $this->respond([
            'message' => 'Retrieved all sessions successfully',
            'sessions' => $modifiedSessions
        ]);
    }


    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function getSession()
    {
        $sessionId = $this->request->sessionId ?? null;

        if (!$sessionId) {
            return $this->failNotFound('Session ID not found. Please log in.');
        }

        $session = $this->sessionService->getSessionById($sessionId);
     
        if (!$session) {
            return $this->failNotFound('Server Session is not active , Please login and try again ');
        }

        return $this->respond([
            'message' => 'Session retrieved successfully',
            'session' => $session,
        ]);
    }


    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function deleteSession(string $id)
    {
        $userId = $this->request->userId ?? null;
        $sessionId = $id;

        if (!$sessionId || !$userId) {
            return $this->failValidationErrors('Invalid session ID or unauthorized request.');
        }

        $deleted = $this->sessionService->deleteSession($sessionId, $userId);

        if (!$deleted) {
            return $this->failNotFound('Session not found or not allowed.');
        }

        return $this->respond([
            'message' => 'Session removed successfully'
        ]);
    }
}
