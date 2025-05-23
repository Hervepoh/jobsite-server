<?php

namespace App\Services;

use App\Models\SessionModel;

class SessionService
{
    public function getAllSessions(string $userId): array
    {
        $model = new SessionModel();

        return $model
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }


    public function getSessionById(string $sessionId): ?object
    {
        $sessionModel = new SessionModel();

        return $sessionModel
            ->select('sessions.*, users.*')
            ->join('users', 'users.id = sessions.user_id')
            ->where('sessions.id', $sessionId)
            ->first();
    }


    public function deleteSession(string $sessionId, string $userId): bool
    {
        $model = new SessionModel();

        // On s'assure que la session appartient bien à cet utilisateur
        $session = $model
            ->where('id', $sessionId)
            ->where('user_id', $userId)
            ->first();

        if (!$session) {
            return false;
        }

        return $model->delete($sessionId);
    }
}
