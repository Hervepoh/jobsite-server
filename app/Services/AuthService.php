<?
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
}