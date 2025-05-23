<?php
namespace App\Services;

use App\Models\VerificationCodeModel;
use App\Models\UserModel;
use Exception;

class AuthService
{
    public function verifyEmail(string $code)
    {
        $verificationModel = new VerificationCodeModel();
        $userModel = new UserModel();

        $validCode = $verificationModel
            ->where('code', $code)
            ->where('type', 'EMAIL_VERIFICATION')
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->first();

        if (!$validCode) {
            throw new Exception("Invalid or expired verification code");
        }

        $updated = $userModel
            ->where('id_utilisateur', $validCode['user_id'])
            ->set(['is_email_verified' => 1])
            ->update();

        if (!$updated) {
            throw new Exception("Unable to verify email address");
        }

        $verificationModel->delete($validCode['id']);
    }

    public function createVerification($userId)
    {

        $verificationModel = new VerificationCodeModel();
        
        $data = [
            'user_id' => $userId,
            'code' =>  bin2hex(random_bytes(32)),
            'type' => 'EMAIL_VERIFICATION',
            'expires_at' => date('Y-m-d H:i:s', strtotime('+'. env('ACTIVATION_TOKEN_EXPIRES' , 3).' hour'))         // Set default expiration time to 3 hours
        ];

        $verificationId = $verificationModel->insert($data, true);
        if (!$verificationId) {
            throw new Exception("Unable to create verification code");
        }
        
        return $verificationModel->find($verificationId);
    }
}