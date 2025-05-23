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
       	$config = Array(
                'protocol' => 'smtp',
                'smtp_host' => 'smtp.office365.com', 
                'smtp_user' => 'noreplyrecruitment@camlight.cm', 
                'smtp_pass' => 'Drh_@20@20', 
                'smtp_port' => 25, 
                'smtp_crypto' => 'tls',
        		//'smtp_timeout' => '15',
                'mailtype'  => 'text',
                'charset'   => 'utf-8', //'iso-8859-1',
                'wordwrap' => TRUE,
                'send_multipart' => FALSE,
                'newline' => "\r\n",

       	);


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
        $this->email->setFrom("noreplyrecruitment@camlight.cm", "Eneo Cameroon S.A. : Job-site");//$destinataire_mail, $nom." ".$prenom);
        $this->email->setReplyTo("");//, $nom." ".$prenom);
        $this->email->setTo(/*"noreplyrecruitment@eneo.cm");//, "Job-site"); //*/$destinataire_mail, $nom." ".$prenom);
        $this->email->setCc("");//$destinataire_mail);//, $nom." ".$prenom);
        $this->email->setBcc("");
        $this->email->setSubject("Job-site : ".$cat);
        $this->email->setMessage($contenu);
        # $this->email->send();

        //Envoi du mail
        if($this->email->send(TRUE))
        {
                $this->session->set_flashdata("dataSuccess", "Merci de nous avoir contacté, nous vous reviendrons.");
        }
        else
        {
		$this->session->set_flashdata("dataError", "Notification de mail non envoyée.");
                //$this->session->set_flashdata("dataError", "Notification de mail non envoyée.<br /> <b>Erreur : </b>".$this->email->printDebugger(['headers']));

        }

        $this->email->clear();
    }


    //Envoi de mail de contact
    public function envoi_mail_compte_cree($destinataire_mail, /*$message, $cat, */$nom, $prenom)
    {
        //Vider la session de mail
        $this->email->clear();
        //Chargement de paramètres d'envoie de mail
        $this->config_mail();

        $contenu = t('mail_inscription'); //"Compte créé avec succès.";

        //Mail
        $this->email->setFrom("noreplyrecruitment@camlight.cm", "Eneo Cameroon S.A. : Job-site");//$destinataire_mail, $nom." ".$prenom);
        $this->email->setReplyTo("");//, $nom." ".$prenom);
        $this->email->setTo(/*"noreplyrecruitment@eneo.cm");//, "Job-site"); //*/$destinataire_mail, $nom." ".$prenom);
        $this->email->setCc("");//$destinataire_mail);//, $nom." ".$prenom);
        $this->email->setBcc("eugene.ndjeme@eneo.cm");
        $this->email->setSubject("Job-site : Compte créé ");//.$cat);
        $this->email->setMessage($contenu);
        # $this->email->send();

        //Envoi du mail
        if($this->email->send(TRUE))
        {
                $this->session->set_flashdata("dataSuccess", "Merci de vous être inscris sur notre plateforme ; un mail a été avoyé à votre adresse. ");
        }
        else
        {
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

        $contenu = "Votre mot de passe a été réinitialisé avec succès, voici votre nouveau mot de passe : ".$message;

        //Mail
        $this->email->setFrom("noreplyrecruitment@camlight.cm", "Eneo Cameroon S.A. : Job-site");//$destinataire_mail, $nom." ".$prenom);
        $this->email->setReplyTo("");//, $nom." ".$prenom);
        $this->email->setTo($destinataire_mail);  /*, $nom." ".$prenom); /*"noreplyrecruitment@eneo.cm");//, "Job-site"); //$destinataire_mail, $nom." ".$prenom);*/
        $this->email->setCc("");//$destinataire_mail);//, $nom." ".$prenom);
        $this->email->setBcc("eugene.ndjeme@eneo.cm");
        $this->email->setSubject("Job-site : Réinitialisaiton de mot de passe");//.$cat);
        $this->email->setMessage($contenu);
        # $this->email->send();

        //Envoi du mail
        if($this->email->send(TRUE))
        {
            $this->session->set_flashdata("dataSuccess", "Merci de vous être inscris sur notre plateforme ; un mail a été avoyé à votre adresse. ");
        }
        else
        {
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
        $this->email->setCc("");//$destinataire_mail);
        $this->email->setBcc("");
        $this->email->setSubject($objet);
        $this->email->setMessage($contenu);
        # $this->email->send();

        //Envoi du mail
        if($this->email->send(TRUE))
        {
                $this->session->set_flashdata("dataSuccess", "Merci d'avoir postulée à cette offre ; un mail a été avoyé à votre adresse. ");
        }
        else
        {
                $this->session->set_flashdata("dataError", "Notification de mail non envoyée.");
                //$this->session->set_flashdata("dataError", "Notification de mail non envoyée.<br /> <b>Erreur : </b>".$this->email->printDebugger(['headers']));

        }

        $this->email->clear();
    }


}
