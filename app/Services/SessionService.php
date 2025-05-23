<?php

namespace App\Services;

use App\Models\SessionModel;

class SessionService
{
    public function getAllSessions(string $userId): array | object
    {
        $model = new SessionModel();

        return $model
            ->where('user_id', $userId)
            ->where('active', 1)
            ->orderBy('timestamp', 'DESC')
            ->findAll();
    }


    public function getSessionById(string $sessionId): ?object
    {
        $sessionModel = new SessionModel();

        return $sessionModel
           // ->select('t_sessions.*, t_utilisateurs.*')
            ->select('t_sessions.*')
            ->join('t_utilisateurs', 't_utilisateurs.id_utilisateur = t_sessions.user_id')
            ->where('t_sessions.id', $sessionId)
            ->where('t_sessions.active', 1)
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

        return $model->update($session->id,["active"=>0]);
    }
}
