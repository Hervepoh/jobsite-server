<?php

namespace App\Models;

use CodeIgniter\Model;

class MailModel extends Model
{
    protected $table            = 't_mails';
    protected $primaryKey       = 'id_mail';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields = [
        'to',
        'subject',
        'content',
        "send",
        'send_at',
        'retry_count',
        'error_message',
        'last_attempt_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    /**
     * Récupère les mails non envoyés
     *
     * @param int $limit
     * @return array
     */
    public function getPendingMails(int $limit = 10): array
    {
        return $this->where('send', 0)
            ->orderBy('created_at', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Marque un mail comme envoyé
     *
     * @param int $id
     */
    public function markAsSent(int $id): bool
    {
        return $this->update($id, [
            'send' => 1,
            'last_attempt_at' => date('Y-m-d H:i:s'),
            'send_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Marque un mail comme échoué
     *
     * @param int $id
     * @param string|null $error
     */
    public function markAsFailed(int $id, ?string $error = null): bool
    {
        return $this->update($id, [
            'send' => 2,
            'error_message' => $error,
        ]);
    }

    public function recordAttempt(int $id, string $error): bool
    {
        $mail = $this->find($id);
        if (!$mail) return false;

        $newRetryCount = $mail->retry_count + 1;
        $sendStatus = ($newRetryCount >= 3) ? 2 : 0;

        return $this->update($id, [
            'retry_count'     => $newRetryCount,
            'send'            => $sendStatus,
            'error_message'   => $error,
            'last_attempt_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
