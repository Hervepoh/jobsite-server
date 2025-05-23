<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Email\Email;

/**
 * Modèles de requête sql pour mysql avec codeigniter
 * Requête simple et usuelles
 **/
class InfoMailModel extends Model
{
    //Configuration du mail
    public function config_mail()
    {
        //Configuration du mail au format
        $config = [
            'protocol'        => env('mail.protocol', 'smtp'),
            'smtp_host'       => env('mail.host'),
            'smtp_user'       => env('mail.user'),
            'smtp_pass'       => env('mail.pass'),
            'smtp_port'       => env('mail.port', 587),
            'smtp_crypto'     => env('mail.crypto', 'tls'),
            //'smtp_timeout' => '15',
            'mailtype'        => env('mail.type', 'text'),
            'charset'         => env('mail.charset', 'utf-8'),
            'wordwrap'        => TRUE,
            'send_multipart'  => FALSE,
            'newline'         => "\r\n",
        ];


        $this->email->initialize($config);

        $this->email->set_newline("\r\n");
    }

    //Envoi de mail de contact
    public function envoi_mail_contact($destinataire_mail, $message, $cat, $nom, $prenom)
    {
        //Vider la session de mail
        $this->email->clear();
        //Chargement de paramètres d'envoie de mail
        $this->config_mail();

        $contenu = $message;

        //Mail
        $this->email->setFrom("noreplyrecruitment@camlight.cm", "Eneo Cameroon S.A. : Job-site"); //$destinataire_mail, $nom." ".$prenom);
        $this->email->setReplyTo(""); //, $nom." ".$prenom);
        $this->email->setTo(/*"noreplyrecruitment@eneo.cm");//, "Job-site"); //*/$destinataire_mail, $nom . " " . $prenom);
        $this->email->setCc(""); //$destinataire_mail);//, $nom." ".$prenom);
        $this->email->setBcc("");
        $this->email->setSubject("Job-site : " . $cat);
        $this->email->setMessage($contenu);
        # $this->email->send();

        //Envoi du mail
        if ($this->email->send(TRUE)) {
            $this->session->set_flashdata("dataSuccess", "Merci de nous avoir contacté, nous vous reviendrons.");
        } else {
            $this->session->set_flashdata("dataError", "Notification de mail non envoyée.");
            //$this->session->set_flashdata("dataError", "Notification de mail non envoyée.<br /> <b>Erreur : </b>".$this->email->printDebugger(['headers']));

        }

        $this->email->clear();
    }


    //Envoi de mail de contact
    public function envoi_mail_compte_cree($destinataire_mail, /*$message, $cat, */ $nom, $prenom, $code)
    {
        // //Vider la session de mail
        // $this->email->clear();
        // //Chargement de paramètres d'envoie de mail
        // $this->config_mail();

        //$contenu = t('mail_inscription'); //"Compte créé avec succès.";
        $contenu = "<br />Bonjour " . $nom . " " . $prenom . ",<br />";
        $contenu .= "Votre compte a été créé avec succès sur notre plateforme de recrutement.<br />";
        $contenu .= "Vous pouvez activer votre compte en cliquant sur le lien ci-dessous :<br />";
        $contenu .= "<a href='" . env('allowedOrigins', 'http://localhost:3000') . "/confirm-account?code=" . $code . "'>Active mon compte</a><br />";
        $contenu .= "Merci de votre confiance et à très bientôt sur notre plateforme de recrutement.<br />";
        $contenu .= "Cordialement,<br />";
        $contenu .= "L'équipe de recrutement d'Eneo Cameroon S.A.<br />";
        $contenu .= "<br />";
        $contenu .= "<br />";
        $contenu .= "<br />";
        $contenu .= "<br />";

        var_dump($contenu);
        die();

        //Mail
        $this->email->setFrom("noreplyrecruitment@camlight.cm", "Eneo Cameroon S.A. : Job-site"); //$destinataire_mail, $nom." ".$prenom);
        $this->email->setReplyTo(""); //, $nom." ".$prenom);
        $this->email->setTo(/*"noreplyrecruitment@eneo.cm");//, "Job-site"); //*/$destinataire_mail, $nom . " " . $prenom);
        $this->email->setCc(""); //$destinataire_mail);//, $nom." ".$prenom);
        $this->email->setBcc("eugene.ndjeme@eneo.cm");
        $this->email->setSubject("Job-site : Compte créé "); //.$cat);
        $this->email->setMessage($contenu);
        # $this->email->send();

        //Envoi du mail
        if ($this->email->send(TRUE)) {
            $this->session->set_flashdata("dataSuccess", "Merci de vous être inscris sur notre plateforme ; un mail a été avoyé à votre adresse. ");
        } else {
            $this->session->set_flashdata("dataError", "Notification de mail non envoyée.");
            //$this->session->set_flashdata("dataError", "Notification de mail non envoyée.<br /> <b>Erreur : </b>".$this->email->printDebugger(['headers']));

        }

        $this->email->clear();
    }


    //Envoi de mail de contact
    public function envoi_mail_reset_pass($destinataire_mail, $message/*, $cat, $nom, $prenom*/)
    {
        //Vider la session de mail
        $this->email->clear();
        //Chargement de paramètres d'envoie de mail
        $this->config_mail();

        $contenu = $message;

        //Mail
        $this->email->setFrom("noreplyrecruitment@camlight.cm", "Eneo Cameroon S.A. : Job-site"); //$destinataire_mail, $nom." ".$prenom);
        $this->email->setReplyTo(""); //, $nom." ".$prenom);
        $this->email->setTo($destinataire_mail);  /*, $nom." ".$prenom); /*"noreplyrecruitment@eneo.cm");//, "Job-site"); //$destinataire_mail, $nom." ".$prenom);*/
        $this->email->setCc(""); //$destinataire_mail);//, $nom." ".$prenom);
        $this->email->setBcc("eugene.ndjeme@eneo.cm");
        $this->email->setSubject("Job-site : Réinitialisaiton de mot de passe"); //.$cat);
        $this->email->setMessage($contenu);
        # $this->email->send();

        //Envoi du mail
        if ($this->email->send(TRUE)) {
            $this->session->set_flashdata("dataSuccess", "Merci de vous être inscris sur notre plateforme ; un mail a été avoyé à votre adresse. ");
        } else {
            $this->session->set_flashdata("dataError", "Notification de mail non envoyée.");
            //$this->session->set_flashdata("dataError", "Notification de mail non envoyée.<br /> <b>Erreur : </b>".$this->email->printDebugger(['headers']));
        }

        $this->email->clear();
    }



    //Envoi de mail de contact
    public function envoi_mail_postule($destinataire_mail, $message, $objet/*, $cat, $nom, $prenom*/)
    {
        //Vider la session de mail
        $this->email->clear();
        //Chargement de paramètres d'envoie de mail
        $this->config_mail();

        $contenu = $message;

        //Mail
        $this->email->setFrom("noreplyrecruitment@camlight.cm", "Eneo Cameroon S.A. : Job-site");
        $this->email->setReplyTo("");
        $this->email->setTo($destinataire_mail/*, $nom." ".$prenom*/);
        $this->email->setCc(""); //$destinataire_mail);
        $this->email->setBcc("");
        $this->email->setSubject($objet);
        $this->email->setMessage($contenu);
        # $this->email->send();

        //Envoi du mail
        if ($this->email->send(TRUE)) {
            $this->session->set_flashdata("dataSuccess", "Merci d'avoir postulée à cette offre ; un mail a été avoyé à votre adresse. ");
        } else {
            $this->session->set_flashdata("dataError", "Notification de mail non envoyée.");
            //$this->session->set_flashdata("dataError", "Notification de mail non envoyée.<br /> <b>Erreur : </b>".$this->email->printDebugger(['headers']));

        }

        $this->email->clear();
    }
}
