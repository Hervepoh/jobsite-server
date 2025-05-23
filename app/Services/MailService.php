<?php

namespace App\Services;

use App\Models\MailModel;
use App\Models\VerificationCodeModel;
use App\Models\UserModel;
use Exception;

class MailService
{
    public function create_account(string $email, string $nom, string $prenom, string $code): bool
    {
        $frontendUrl = rtrim(env('allowedOrigins', 'http://localhost:3000'), '/');

        $content = "
            Bonjour {$nom} {$prenom},<br /><br />
            Votre compte a été créé avec succès sur notre plateforme de recrutement.<br />
            Veuillez activer votre compte en cliquant sur le lien ci-dessous :<br />
            <a href='{$frontendUrl}/confirm-account?code={$code}'>Activer mon compte</a><br /><br />
            Merci de votre confiance et à très bientôt sur notre plateforme de recrutement.<br /><br />
            Cordialement,<br />
            L'équipe de recrutement d'Eneo Cameroon S.A.
        ";

        try {
            $mailModel = new MailModel();
            return $mailModel->insert([
                'to'      => $email,
                'subject' => 'Création de compte Eneo Cameroon S.A.',
                'content' => $content,
                'send'    => 0, // en attente d’envoi
            ]) !== false;
        } catch (Exception $e) {
            log_message('error', 'Erreur lors de l\'enregistrement de l’e-mail : ' . $e->getMessage());
            return false;
        }
    }
}
