<?php 

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modèles de requête sql pour mysql avec codeigniter
 * Requête simple et usuelles
**/ 
class DataModel extends Model
{

	
	//Renvoie le nombre d'enregistrement d'une table
	public function select_count_nombre($table, $element)
	{
		return $this->db->table($table)
						->select($element)
						->countAllResults();
	}
	public function count_element($table, $critere)
	{
		return $this->db->table($table)
						->select('*')
						->where($critere)
						->countAllResults();
	}
	
	public function count_element_critere($table, $element, $critere)
	{
		return $this->db->table($table)
						->select($element)
						->where($critere)
						->countAllResults();
	}

	//Renvoie la somme d'un champ d'enregistrement d'une table
	public function sum_element($table, $critere, $val)
	{
		return $this->db->table($table)
						->selectSum($val)
						->where($critere)
						->get()
						->getResult();
	}

	//Renvoie les éléments unique d'une table d'enregistrement d'une table
	public function distinct_element($table, $critere, $val)
	{
		return $this->db->table($table)
						->distinct($val)
						->where($critere)
						->get()
						->getResult();
	}

	//Récupérer des enregistrements dans une table selon un critère
	public function select_one($table, $key, $val_key)
	{
		return $this->db->table($table)
						->select('*')        				
        				->where($key, $val_key)
        				->get()
						->getResult(); 
	}

	public function select_data($table, $data)
	{
		return $this->db->table($table)
        				->select('*') 
        				->where($data)
        				->get()
						->getResult(); 
	}
	
	

	public function select_data_limit($table, $data, $nbre)
	{
		return $this->db->table($table)
        				->select('*')
        				->where($data)
        				->limit($nbre)
        				->get()
						->getResult(); 
	}
	

	public function select_all_limit($table, $limit, $par_page, $ordre)
	{
		return $this->db->table($table)
						->select('*')
        				->limit($limit, $par_page)
						->orderBy($ordre, 'asc')
        				->get()
        				->getResult(); 
	}
	
	public function select_like($table, $key, $val_key)
	{
		return $this->db->table($table)
        				->select('*')
        				->like($key, $val_key)
        				->get()
						->getResult(); 
	}
	
	//Execute query
    public function execute_query($query)
	{
		 $res = $this->db->query($query)->getResult();
		 $result = $res
						;
		return 	$result; 
	}
	//Récupérer des enregistrements dans une table selon un ou plusieurs critère(s)
	public function select_one_plus($table, $donnee)
	{
		return $this->db->table($table)
						->select('*')
        				->where($donnee)
        				->get()
						->getResult(); 
	}
	
	public function select_one_plus2($table, $donnee, $key)
	{
		return $this->db->table($table)
						->select('*')
        				->where($donnee)
        				->orderBy($key, 'desc')
        				->get()
						->getResult(); 
	}
	
	public function select_one_plus_order($table, $donnee, $key, $rang)
	{
		return $this->db->table($table)->select('*')
        				
        				->where($donnee)
        				->orderBy($key, $rang)
        				->get()
        				
						->getResult(); 
	}
	
	public function select_one_plus3($cherche, $table, $donnee)
	{
		return $this->db->table($table)
						->select($cherche)
        				->where($donnee)
        				->get()
						->getResult(); 
	}

	public function select_one_plus_in($cherche, $table, $in, $valIn)
	{
		return $this->db->table($table)
						->select($cherche)
        				->whereIn($in, $valIn)
        				->get()
						->getResult(); 
	}
	
	public function select_one_plus4($cherche, $table, $donnee, $ordre, $nbre)
	{
		return $this->db->table($table)
						->select($cherche)
        				->where($donnee)
        				->orderBy($ordre, 'desc')
        				->limit($nbre)
        				->get()
						->getResult(); 
	}

	//Récupérer tous les lenregistrements d'une table
	public function select_all($table)
	{
		return $this->db->table($table)
						->select('*')
        				->get()
						->getResult(); 
	}

	//Récupérer tous les lenregistrements d'une table
	public function select_all2($table, $key)
	{
		return $this->db->table($table)
						->select('*')
        				->orderBy($key, 'asc')
        				->get() 
						->getResult(); 
	}

	//Récupérer des enregistrements dans une table selon un critère
	public function select_key_element($table, $element)
	{
		return $this->db->table($table)
						->select($element)
        				->get()
						->getResult(); 
	}

	public function select_all_element_by_key_ordre($table, $element, $key, $ordre)
	{
		return $this->db->table($table)
						->select($element)
        				->where($key)
						->orderBy($ordre, 'asc')
        				->get()
        				->getResult(); 
	}

	//insert les données dans une tables
	public function insert_data($table, $data)
	{	
		if($this->db->table($table)->set($data)->insert())
		{
			return $this->db->insertID();
		}
		else 
		{
			return $this->db->error();
		}
    }

    //mise à jour des données 
    public function update_data($table, $data, $dataKey)
	{
		if($this->db->table($table)->set($data)->where($dataKey)->update())
		{
		    //echo $this->db->last_query();
		    //die();
			return true;
		}
		else 
		{
			return $this->db->error();
		}
	}
	
	 public function update_data1($id, $data,$key,$table)
	{
		$pers=$this->db->table($table)->set($data)->where($key, $id)->update();
		if($pers)
		{
			return $pers;
		}
		else 
		{
			$pers=$this->insert_data($table, $data);
			return $pers;
		}
	}

	//Supprimer les données d'une table suivant un critère
	public function delete_one_data($table, $data)
	{
		if($this->db->table($table)->where($data)->delete())
		{
			return true;
		}
		else 
		{
			return $this->db->error();
		}
	}

	/**
	 * Cryptage de mot de passe
	**/
	public function crypt_password($pass)
	{
		//cryptage du mot de passe 
		$key = $pass;
		$cout = ['cost' => 7]; //Cout de l'application (temps de cryptage)
		$algo = PASSWORD_BCRYPT; //algorithme de cryptage
		$passValide = password_hash($key, $algo, $cout); //Cryptage
	
		return $passValide;
	}
	
	/**
	 * Test du de mot de passe
	**/
	public function error_pass($key, $val_key)
	{
		$pass = $key;
		$passUser = $val_key;
		
		if(password_verify($pass, $passUser)) 
		{
			return "ok";
		}
		else
		{
			return "error";
		}
	}

	/**
	 * Vériication de la complexité du mot de passe.
	**/
	public function complexite_password($mdp)	
	{ 
		// $mdp le mot de passe passé en paramètre
 
		// On récupère la longueur du mot de passe	
		$longueur = strlen($mdp);
		$point = 0;
		$pointMin = 0;
		$pointMaj = 0;
		$pointChiffre = 0;
		$pointCaracteres = 0;
		$point_maj = 0;
		$point_min = 0;
		$point_chiffre = 0; 
		$point_caracteres = 0;

		if ($longueur < 8) 
		{
			return $taill_pass = 'Le mot de passe doit avoir au moins 8 caractères';
		}
		 
		// On fait une boucle pour lire chaque lettre
		for($i = 0; $i < $longueur; $i++) 	
		{
			// On sélectionne une à une chaque lettre
			// $i étant à 0 lors du premier passage de la boucle
			$lettre = $mdp[$i];
		 
			if ($lettre>='a' && $lettre<='z')
			{
				// On ajoute 1 point pour une minuscule
				$point = $point + 1;
				$pointMin = $pointMin + 1;
		 
				// On rajoute le bonus pour une minuscule
				$point_min = 1;
			}
			else if ($lettre>='A' && $lettre <='Z')
			{
				// On ajoute 2 points pour une majuscule
				$point = $point + 2;
				$pointMaj = $pointMaj + 1;
		 
				// On rajoute le bonus pour une majuscule
				$point_maj = 2;
			}
			else if ($lettre>='0' && $lettre<='9')
			{
				// On ajoute 3 points pour un chiffre
				$point = $point + 3;
				$pointChiffre = $pointChiffre + 1;
		 
				// On rajoute le bonus pour un chiffre
				$point_chiffre = 3;
			}
			else 
			{
				// On ajoute 5 points pour un caractère autre
				$point = $point + 5;
				$pointCaracteres = $pointCaracteres + 1;
		 
				// On rajoute le bonus pour un caractère autre
				$point_caracteres = 5;
			}
		}

		if ($pointMin == null || $pointMaj == null || $pointChiffre == null || $pointCaracteres == null) 
		{
			return $complex_pass = 'Le mot de passe doit avoir au moins une lettre majuscule, minuscule, un chiffre et un caractère spécial';
		}
		 
		// Calcul du coefficient points/longueur
		$etape1 = $point / $longueur;
		 
		// Calcul du coefficient de la diversité des types de caractères...
		$etape2 = $point_min + $point_maj + $point_chiffre + $point_caracteres;
		 
		// Multiplication du coefficient de diversité avec celui de la longueur
		$resultat = $etape1 * $etape2;
		 
		// Multiplication du résultat par la longueur de la chaîne
		$final = $resultat * $longueur;
		 
		return $final;
	}
	
	/**
	 * Vérification de la date
	**/
	public function valide_date($jour, $mois, $anne, $redirectLink)
	{
        //Vériication des moi de 30 jours
        if($jour == 31)
        {
            switch ($mois)
			{
				case '4': 
					$this->session->set_flashdata("dataError", "Le ".$mois."ième mois de l'année ne peut pas avoir 31 jours");
					redirect($redirectLink, 'location');
				break;
				case '6': 
					$this->session->set_flashdata("dataError", "Le ".$mois."ième mois de l'année ne peut pas avoir 31 jours");
					redirect($redirectLink, 'location');
				break;
				case '9': 
					$this->session->set_flashdata("dataError", "Le ".$mois."ième mois de l'année ne peut pas avoir 31 jours");
					redirect($redirectLink, 'location');
				break;
				case '11': 
					$this->session->set_flashdata("dataError", "Le ".$mois."ième mois de l'année ne peut pas avoir 31 jours");
					redirect($redirectLink, 'location');
				break;

			}
		}


		$fevrierEnCours_nbreJours = date('t', strtotime("february")); 
        //mois de février
        if(($jour == 29) && ($mois == 2)) 
        {
        	$fevrierEnCours_nbreJours = date('t', strtotime("february ".$anne)); //On récupère le nombre de jours de février de l'année fourni
        	if ($fevrierEnCours_nbreJours < $jour) 
        	{
        		//Méssage de retour
				$this->session->set_flashdata("dataError", "Ce mois de février a ".$fevrierEnCours_nbreJours." jours pour l'annéé ".$anne." Vous avez mis ".$jour."/".$mois."/".$anne);
	            redirect($redirectLink, 'location');
        	}
        	
        }

        //mois de février
        if(($jour > 29) && ($mois == 2)) 
        {
        	//Méssage de retour
			$this->session->set_flashdata("dataError", "Le mois de février ne peut dépasser 29 jours. Vous avez mis ".$jour."/".$mois."/".$anne);
            redirect($redirectLink, 'location');
        }

	}

	/**
	 * Remplace tous les accents par leur équivalent sans accent.
	**/
    public function skip_accents_extratcion($chaine)
    {
     	$search  = array(';', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ');
     	
		//Préférez str_replace à strtr car strtr travaille directement sur les octets, ce qui pose problème en UTF-8
		$replace = array(',', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y');

		$varMaChaine = str_replace($search, $replace, $chaine);
		return $varMaChaine; //On retourne le résultat
    } 
}