<?php

namespace App\Models;

use CodeIgniter\Model;

class VerificationCodeModel extends Model
{
    protected $table      = 't_verification_codes';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = [
        'user_id',
        'code',
        'type',
        'expires_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
