<?php

namespace App\Controllers\Api;

use App\Models\DataModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use \Exception;

class CvController extends ResourceController
{
    protected $format = 'json';

    private $lang = 't_langues';
    private $cursus = 't_cursus';
    private $forme = "t_formations";
	private $gestion = 't_gestion_cv_saves';
	private $v_forme = "v_formations";
    private $associe = 't_activite_associatives';
    private $atteste = 't_attestations';
    private $compet = "t_competences";

    private $su = '';
    protected $model = null;
    private $errorForm = "";
    private $msgSuccess = "";
    private $msgError = "";
    private $msgInfo = "";

    //Constructeur parent
    public function __construct()
    {
        $this->msgSuccess = lang('message.msg_succes');
        $this->msgError = lang('message.error');
        $this->msgInfo = lang('message.error_info');
        $this->model = new DataModel();
    }


    /**
     * Langues
     * @return ResponseInterface
     */
    public function save_langue(): ResponseInterface
    {
        $userId = $this->request->userId ?? null;
        $request = $this->request->getJSON(true);

        $langue = $request['langue'];
        $niveau_langue_parle = $request['niveauLangueParle'];
        $niveau_langue_ecris = $request['niveauLangueEcris'];

        if ($langue && $niveau_langue_ecris && $niveau_langue_parle) {
            //Eléments de sauvegarde de la langue
            $langue_array = array(
                'nom_langue' => $langue,
                'niveau_orale' => $niveau_langue_parle,
                'niveau_ecris' => $niveau_langue_ecris,
                'utilisateur_langue ' => $userId
            );
        } else {
            $data = $this->msgError . " : Veuillez renseigner la langue, le niveau parlé et/ou écrit de votre compétence!";
            return $this->response
                ->setStatusCode(422)
                ->setJSON(json_encode([
                    'status' => 'error',
                    'message' => $data,
                ]));
        }

        try {
            $insertLangue = $this->model->insert_data($this->lang, $langue_array);
            $data = $this->msgSuccess;
            return $this->response
                ->setStatusCode(200)
                ->setJSON(json_encode([
                    'status' => 'success',
                    'message' => $data,
                ]));
        } catch (Exception $e) {
            $data = $this->msgError . " : " . $e->getMessage();
            return $this->response
                ->setStatusCode(400)
                ->setJSON(json_encode([
                    'status' => 'error',
                    'message' => $data,
                ]));
        }
    }

    /**
     * Langues
     * @return ResponseInterface
     */
    public function update_langue($id): ResponseInterface
    {
        $userId =   $this->request->userId ?? null;
        $request = $this->request->getJSON(true);

        $langue = $request['langue'];
        $niveau_langue_parle = $request['niveauLangueParle'];
        $niveau_langue_ecris = $request['niveauLangueEcris'];

        if ($langue && $niveau_langue_ecris && $niveau_langue_parle) {
            //Eléments de sauvegarde de la langue
            $langue = array(
                'nom_langue' => $langue,
                'niveau_orale' => $niveau_langue_parle,
                'niveau_ecris' => $niveau_langue_ecris,
                'utilisateur_langue ' => $userId
            );
        } else {
            $data = "Veuillez renseigner la langue, le niveau parlé et/ou écrit de votre compétence!";
            return $this->response
                ->setStatusCode(422)
                ->setJSON(json_encode([
                    'status' => 'error',
                    'message' => $data,
                ]));
        }

        try {
            $key = array('id_langue' => $id);
            $updateLangue = $this->model->update_data($this->lang, $langue, $key);

            $data = $this->msgSuccess;
            return $this->response
                ->setStatusCode(200)
                ->setJSON(json_encode([
                    'status' => 'success',
                    'message' => $data,
                ]));
        } catch (Exception $e) {
            $data = $this->msgError . " : " . $e->getMessage();
            return $this->response
                ->setStatusCode(400)
                ->setJSON(json_encode([
                    'status' => 'error',
                    'message' => $data,
                ]));
        }
    }

    /**
     * Langues
     * @return ResponseInterface
     */
    public function delete_langue($id): ResponseInterface
    {
        if (is_null($id)) {
            $data = $this->msgError;
            return $this->response->setJSON(json_encode($data));
        }

        try {
            $key = array('id_langue' => $id);
            $deleteLang = $this->model->delete_one_data($this->lang, $key);

            $data = $this->msgSuccess;
            return $this->response
                ->setStatusCode(200)
                ->setJSON(json_encode([
                    'status' => 'success',
                    'message' => $data,
                ]));
        } catch (Exception $e) {
            $data = $this->msgError . " : " . $e->getMessage();
            return $this->response
                ->setStatusCode(400)
                ->setJSON(json_encode([
                    'status' => 'error',
                    'message' => $data,
                ]));
        }
    }

    /**
     * Fonction de recupération de la liste 
     * Langue
     */
    public function getAll_langues()
    {
        $userId =   $this->request->userId ?? null;
        $clause_where = array('utilisateur_langue' => $userId);
        try {
            $data = $this->model->select_data($this->lang, $clause_where);
            return $this->response
                ->setStatusCode(200)
                ->setJSON(json_encode([
                    'status' => 'success',
                    'langue' => $data,
                ]));
        } catch (Exception $e) {
            $data = $this->msgError . " : " . $e->getMessage();
            return $this->response
                ->setStatusCode(400)
                ->setJSON(json_encode([
                    'status' => 'error',
                    'message' => $data,
                ]));
        }
    }

    /**
     * Cursus
     * @return ResponseInterface
     */
    public function save_cursus(): ResponseInterface
    {
        $userId = $this->request->userId ?? null;
        $request = $this->request->getJSON(true);

        //Vérification de la case 'Aucun Cursus' cochée
        if (!$request['no-cursus']) {

            $libelleFormation = $request['libelle_formation'];
            $nomCentreFormation = $request['nom_centre_formation'];
            $heureHeureFormation = $request['heure_heure_formation'];

            //Debut
            $jourDebut = $request['jourFormDebut'];
            $moisDebut = $request['moisFormDebut'];
            $anneeDebut = $request['anneeFormDebut'];

            //$valideDateFin = $this->Utilisateur_model->valide_date($jourDebut, $moisDebut, $anneeDebut, $redirectLink);
            $debutEmploi = $jourDebut . '/' . $moisDebut . '/' . $anneeDebut;

            //date du jour
            $j = date('j');
            $m = date('n');
            $a = date('Y');

            $date_day = $j . "/" . $m . "/" . $a;

            if (($a < $anneeDebut) || (($a == $anneeDebut) && ($m < $moisDebut)) || (($a == $anneeDebut) && ($m == $moisDebut) && ($j < $jourDebut))) {
                $data = $this->msgError . " : " . "les dates ne concordent pas.";
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON(json_encode([
                        'status' => 'error',
                        'message' => $data,
                    ]));
            }

            $jourFin = $request['jourFormFin'];
            $moisFin = $request['moisFormFin'];
            $anneeFin = $request['anneeFormFin'];

            $finEmploi = $jourFin . '/' . $moisFin . '/' . $anneeFin;

            $paysFormation = $request['pays_formation'];
            $infoSuplement = $request['info_suplement'];
            $commentaire = $request['commentaire'];

            if ($anneeDebut > $anneeFin) {
                $data = $this->msgError . " : " . "les dates ne concordent pas.";
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON(json_encode([
                        'status' => 'error',
                        'message' => $data,
                    ]));
            } elseif ($anneeDebut == $anneeFin) {
                if ($moisDebut > $moisFin) {
                    $data = $this->msgError . " : " . "les dates ne concordent pas.";
                    return $this->response
                        ->setStatusCode(422)
                        ->setJSON(json_encode([
                            'status' => 'error',
                            'message' => $data,
                        ]));
                } elseif ($moisDebut == $moisFin) {
                    if ($jourDebut > $jourFin) {
                        $data = $this->msgError . " : " . "les dates ne concordent pas.";
                        return $this->response
                            ->setStatusCode(422)
                            ->setJSON(json_encode([
                                'status' => 'error',
                                'message' => $data,
                            ]));
                    }
                }
            }

            //Eléments de sauvegarde
            $cursus = array(
                'libelle_formation' => $libelleFormation,
                'nom_centre_formation' => $nomCentreFormation,
                'heure_heure_formation' => $heureHeureFormation,
                'date_debut_cursus' => $debutEmploi,
                'jour_debut_cursus' => $jourDebut,
                'mois_debut_cursus	' => $moisDebut,
                'annee_debut_cursus' => $anneeDebut,

                'date_fin_cursus' => $finEmploi,
                'jour_fin_cursus' => $jourFin,
                'mois_fin_cursus' => $moisFin,
                'annee_fin_cursus' => $anneeFin,
                'pays_formation' => $paysFormation,
                'info_suplement' => $infoSuplement,
                'commentaire' => $commentaire,
                'date_create_cursus' => time(),
                'utilisateur_cursus' => $userId
            );
        }

        try {
            $insertCursus = $this->model->insert_data($this->cursus, $cursus);
            $data = $this->msgSuccess;
            return $this->response
                ->setStatusCode(200)
                ->setJSON(json_encode([
                    'status' => 'success',
                    'message' => $data,
                ]));
        } catch (Exception $e) {
            $data = $this->msgError . " : " . $e->getMessage();
            return $this->response
                ->setStatusCode(400)
                ->setJSON(json_encode([
                    'status' => 'error',
                    'message' => $data,
                ]));
        }
    }

     /**
     * Cursus
     * @return ResponseInterface
     */
    public function update_cursus($id): ResponseInterface
	{
        $userId = $this->request->userId ?? null;
        $request = $this->request->getJSON(true);

		if(!$request['no-cursus']){
			$libelleFormation = $request['libelle_formation'];
			$nomCentreFormation = $request['nom_centre_formation'];
			$heureHeureFormation = $request['heure_heure_formation'];
	
			//Debut
			$jourDebut = $request['jourFormDebut'];
			$moisDebut = $request['moisFormDebut'];
			$anneeDebut = $request['anneeFormDebut'];
	
			//$valideDateFin = $this->Utilisateur_model->valide_date($jourDebut, $moisDebut, $anneeDebut, $redirectLink);
	
			$debutEmploi = $jourDebut . '/' . $moisDebut . '/' . $anneeDebut;
	
			//date du jour
			$j = date('j');
			$m = date('n');
			$a = date('Y');
	
			$date_day = $j . "/" . $m . "/" . $a;
	
			if (($a < $anneeDebut) || (($a == $anneeDebut) && ($m < $moisDebut)) || (($a == $anneeDebut) && ($m == $moisDebut) && ($j < $jourDebut))) {
				$data = $this->msgError . " : " . "les dates ne concordent pas.";
				return $this->response
					->setStatusCode(422)
					->setJSON(json_encode([
						'status' => 'error',
						'message' => $data,
					]));
			}
			//$dateFin = $request['date_fin'];
	
			//Fin
			$jourFin = $request['jourFormFin'];
			$moisFin = $request['moisFormFin'];
			$anneeFin = $request['anneeFormFin'];
	
			//$valideDateFin = $this->Utilisateur_model->valide_date($jourFin, $moisFin, $anneeFin, $redirectLink);
	
			$finEmploi = $jourFin . '/' . $moisFin . '/' . $anneeFin;
	
			$paysFormation = $request['pays_formation'];
			$infoSuplement = $request['info_suplement'];
			$commentaire = $request['commentaire'];
	
			if ($anneeDebut > $anneeFin) {
				$data = $this->msgError . " : " . "les dates ne concordent pas.";
				return $this->response
					->setStatusCode(422)
					->setJSON(json_encode([
						'status' => 'error',
						'message' => $data,
					]));
			} elseif ($anneeDebut == $anneeFin) {
				if ($moisDebut > $moisFin) {
					$data = $this->msgError . " : " . "les dates ne concordent pas.";
					return $this->response
						->setStatusCode(422)
						->setJSON(json_encode([
							'status' => 'error',
							'message' => $data,
						]));
				} elseif ($moisDebut == $moisFin) {
					if ($jourDebut > $jourFin) {
						$data = $this->msgError . " : " . "les dates ne concordent pas.";
						return $this->response
							->setStatusCode(422)
							->setJSON(json_encode([
								'status' => 'error',
								'message' => $data,
							]));
					}
				}
			}
	
			//Eléments de sauvegarde
			$cursus = array(
				'libelle_formation' => $libelleFormation,
				'nom_centre_formation' => $nomCentreFormation,
				'heure_heure_formation' => $heureHeureFormation,
				'date_debut_cursus' => $debutEmploi,
				'jour_debut_cursus' => $jourDebut,
				'mois_debut_cursus	' => $moisDebut,
				'annee_debut_cursus' => $anneeDebut,
	
				'date_fin_cursus' => $finEmploi,
				'jour_fin_cursus' => $jourFin,
				'mois_fin_cursus' => $moisFin,
				'annee_fin_cursus' => $anneeFin,
				'pays_formation' => $paysFormation,
				'info_suplement' => $infoSuplement,
				'commentaire' => $commentaire,
				'date_update_cursus' => time(),
				'utilisateur_cursus' => $userId
			);
		}

		try {
			$key = array('id_cursus' => $id);
			$updateCursus = $this->model->update_data($this->cursus, $cursus, $key);

			$data = $this->msgSuccess;
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'message' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}

    /**
     * Cursus
     * @return ResponseInterface
     */
    public function delete_cursus($id): ResponseInterface
	{
		if (is_null($id)) {
			$data = $this->msgError;
			return $this->response
				->setStatusCode(422)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}

		try {
			$key = array('id_cursus' => $id);
			$deleteCursus = $this->model->delete_one_data($this->cursus, $key);

			$data = $this->msgSuccess;
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'message' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}

    /**
	 * Fonction de Liste
	 * Cursus complémentaire
	 **/
	public function getAll_cursus()
	{
        $userId = $this->request->userId ?? null;
 
		$clause_where = array('utilisateur_cursus' => $userId);
		try {
			$data = $this->model->select_data($this->cursus, $clause_where);
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'cursus' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}

    /**
     * Formation
     * @return ResponseInterface
     */
    public function save_formation(): ResponseInterface
	{
        $userId = $this->request->userId ?? null;
        $request = $this->request->getJSON(true);

		//Vérification si la case "Aucune Formation" est coché
		if (!$request['no-training']) {
			//Debut
			$jourDebut = $request['jourFormDebut'];
			$moisDebut = $request['moisFormDebut'];
			$anneeDebut = $request['anneeFormDebut'];
			$debutEmploi = $jourDebut . '/' . $moisDebut . '/' . $anneeDebut;
			//date du jour
			$j = date('j');
			$m = date('n');
			$a = date('Y');
			$date_day = $j . "/" . $m . "/" . $a;

			if (($a < $anneeDebut) || (($a == $anneeDebut) && ($m < $moisDebut)) || (($a == $anneeDebut) && ($m == $moisDebut) && ($j < $jourDebut))) {
				$data = $this->msgError . " : " . "les dates ne concordent pas.";
				return $this->response
					->setStatusCode(422)
					->setJSON(json_encode(
						[
							'status' => 'error',
							'message' => $data,
						]
					));
			}

			//Fin
			$jourFin = $request['jourFormFin'];
			$moisFin = $request['moisFormFin'];
			$anneeFin = $request['anneeFormFin'];
			$finEmploi = $jourFin . '/' . $moisFin . '/' . $anneeFin;

			$statut = $request['statut'];

			if ($statut == '1') {
				if (($a < $anneeFin) || (($a == $anneeFin) && ($m < $moisFin)) || (($a == $anneeFin) && ($m == $moisFin) && ($j < $jourFin))) {
					$data = $this->msgError . " : " . "les dates ne concordent pas.";
					return $this->response
						->setStatusCode(422)
						->setJSON(json_encode(
							[
								'status' => 'error',
								'message' => $data,
							]
						));
				}
			}

			if ($statut == '0') {
				if (($a > $anneeFin) || (($a == $anneeFin) && ($m > $moisFin)) || (($a == $anneeFin) && ($m == $moisFin) && ($j > $jourFin))) {
					$data = $this->msgError . " : " . "les dates ne concordent pas.";
					return $this->response
						->setStatusCode(422)
						->setJSON(json_encode(
							[
								'status' => 'error',
								'message' => $data,
							]
						));
				}
			}

			$ecole = $request['ecole'];
			$diplome = $request['diplome'];
			$domaine = $request['domaine'];
			$special = $request['special'];

			if ($anneeDebut > $anneeFin) {
				$data = $this->msgError . " : " . "les dates ne concordent pas.";
				return $this->response
					->setStatusCode(422)
					->setJSON(json_encode(
						[
							'status' => 'error',
							'message' => $data,
						]
					));
			} elseif ($anneeDebut == $anneeFin) {
				if ($moisDebut > $moisFin) {
					$data = $this->msgError . " : " . "les dates ne concordent pas.";
					return $this->response
						->setStatusCode(422)
						->setJSON(json_encode(
							[
								'status' => 'error',
								'message' => $data,
							]
						));
				} elseif ($moisDebut == $moisFin) {
					if ($jourDebut > $jourFin) {
						$data = $this->msgError . " : " . "les dates ne concordent pas.";
						return $this->response
							->setStatusCode(422)
							->setJSON(json_encode(
								[
									'status' => 'error',
									'message' => $data,
								]
							));
					}
				}
			}

			$formation = array(
				'date_debut_formation' => $debutEmploi,
				'jour_debut_formation' => $jourDebut,
				'mois_debut_formation' => $moisDebut,
				'annee_debut_formation' => $anneeDebut,

				'date_fin_formation' => $finEmploi,
				'jour_fin_formation' => $jourFin,
				'mois_fin_formation' => $moisFin,
				'annee_fin_formation' => $anneeFin,

				'statut_formation' => $statut,
				'diplome' => $diplome,
				'ecole' => $ecole,
				'domaine_formation' => $domaine,
				'specialite' => $special,
				'utilisateur_formation' => $userId
			);
		}

		try {
			$insertFormation = $this->model->insert_data($this->forme, $formation);

			$this->add_formation_in_gestion_cv();

			$data = $this->msgSuccess;
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode(
					[
						'status' => 'success',
						'message' => $data,
					]
				));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode(
					[
						'status' => 'error',
						'message' => $data,
					]
				));
		}
	}

     /**
     * Formation
     * @return ResponseInterface
     */
    public function update_formation($id): ResponseInterface
	{
        $userId = $this->request->userId ?? null;
        $request = $this->request->getJSON(true);

		if(!$request["no-training"]){
			//Debut
			$jourDebut = $request['jourFormDebut'];
			$moisDebut = $request['moisFormDebut'];
			$anneeDebut = $request['anneeFormDebut'];
			$debutEmploi = $jourDebut . '/' . $moisDebut . '/' . $anneeDebut;
	
			//date du jour
			$j = date('j');
			$m = date('n');
			$a = date('Y');
	
			$date_day = $j . "/" . $m . "/" . $a;
			if (($a < $anneeDebut) || (($a == $anneeDebut) && ($m < $moisDebut)) || (($a == $anneeDebut) && ($m == $moisDebut) && ($j < $jourDebut))) {
				$data = $this->msgError . " : " . "les dates ne concordent pas.";
				return $this->response
					->setStatusCode(422)
					->setJSON(json_encode(
						[
							'status' => 'error',
							'message' => $data,
						]
					));
			}
	
			//Fin
			$jourFin = $request['jourFormFin'];
			$moisFin = $request['moisFormFin'];
			$anneeFin = $request['anneeFormFin'];
			$finEmploi = $jourFin . '/' . $moisFin . '/' . $anneeFin;
	
			$statut = $request['statut'];
	
			if ($statut == '1') {
				if (($a < $anneeFin) || (($a == $anneeFin) && ($m < $moisFin)) || (($a == $anneeFin) && ($m == $moisFin) && ($j < $jourFin))) {
					$data = $this->msgError . " : " . "les dates ne concordent pas.";
					return $this->response
						->setStatusCode(422)
						->setJSON(json_encode(
							[
								'status' => 'error',
								'message' => $data,
							]
						));
				}
			}
	
			if ($statut == '0') {
				if (($a > $anneeFin) || (($a == $anneeFin) && ($m > $moisFin)) || (($a == $anneeFin) && ($m == $moisFin) && ($j > $jourFin))) {
					$data = $this->msgError . " : " . "les dates ne concordent pas.";
					return $this->response
						->setStatusCode(422)
						->setJSON(json_encode(
							[
								'status' => 'error',
								'message' => $data,
							]
						));
				}
			}
	
			$ecole = $request['ecole'];
			$diplome = $request['diplome'];
			$domaine = $request['domaine'];
			$special = $request['special'];
	
			if ($anneeDebut > $anneeFin) {
				$data = $this->msgError . " : " . "les dates ne concordent pas.";
				return $this->response
					->setStatusCode(422)
					->setJSON(json_encode(
						[
							'status' => 'error',
							'message' => $data,
						]
					));
			} elseif ($anneeDebut == $anneeFin) {
				if ($moisDebut > $moisFin) {
					$data = $this->msgError . " : " . "les dates ne concordent pas.";
					return $this->response
						->setStatusCode(422)
						->setJSON(json_encode(
							[
								'status' => 'error',
								'message' => $data,
							]
						));
				} elseif ($moisDebut == $moisFin) {
					if ($jourDebut > $jourFin) {
						$data = $this->msgError . " : " . "les dates ne concordent pas.";
						return $this->response
							->setStatusCode(422)
							->setJSON(json_encode(
								[
									'status' => 'error',
									'message' => $data,
								]
							));
					}
				}
			}
	
			$formation = array(
				'date_debut_formation' => $debutEmploi,
				'jour_debut_formation' => $jourDebut,
				'mois_debut_formation' => $moisDebut,
				'annee_debut_formation' => $anneeDebut,
	
				'date_fin_formation' => $finEmploi,
				'jour_fin_formation' => $jourFin,
				'mois_fin_formation' => $moisFin,
				'annee_fin_formation' => $anneeFin,
	
				'statut_formation' => $statut,
				'diplome' => $diplome,
				'ecole' => $ecole,
				'specialite' => $special,
				'domaine_formation' => $domaine,
				'utilisateur_formation' => $userId
			);
		}

		try {
			$key = array('id_formation' => $id);
			$updateFormation = $this->model->update_data($this->forme, $formation, $key);

			$this->add_formation_in_gestion_cv();

			$data = $this->msgSuccess;
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode(
					[
						'status' => 'success',
						'message' => $data,
					]
				));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode(
					[
						'status' => 'error',
						'message' => $data,
					]
				));
		}
	}

      /**
     * Formation
     * @return ResponseInterface
     */
    public function delete_formation($id): ResponseInterface
	{
		if (is_null($id)) {
			$data = $this->msgError;
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode(
					[
						'status' => 'error',
						'message' => $data,
					]
				));
		}

		try {
			$key = array('id_formation' => $id);
			$deleteExperience = $this->model->delete_one_data($this->forme, $key);

			$data = $this->msgSuccess;
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode(
					[
						'status' => 'success',
						'message' => $data,
					]
				));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode(
					[
						'status' => 'error',
						'message' => $data,
					]
				));
		}
	}

    /**
	 * Fonction de recupération de la liste 
	 * Formation
	 */
	public function getAll_Formations()
	{
        $userId = $this->request->userId ?? null;
		$clause_where = array('utilisateur_formation' => $userId);
		try {
			$data = $this->model->select_data($this->forme, $clause_where);
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'formation' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}

    public function add_formation_in_gestion_cv()
	{
        $userId = $this->request->userId ?? null;
		//Mise à jour des formations dans l'ordre la table t_gestion_cv
		$critere = array('utilisateur_formation' => $userId);
		$order = "id_niveau_etude";
		$formation = $this->model->select_one_plus_order($this->v_forme, $critere, $order, "desc");

		if (!empty($formation)) {
			$nbr = count($formation);
			$grand = 4;
			if ($nbr >= 4) {
				$grand = $grand;
			} elseif ($nbr < 4) {
				$grand = $nbr + 1;
			}

			//foreach ($formation as $form )
			for ($i = 1; $i < $grand; $i++) { // premier tour ; $i = 1
				$r = $i - 1;

				$info_etude =  array(
					'comp_nom_diplome' . $i => $formation[$r]->nom_diplome,
					'comp_nom_specialite' . $i => $formation[$r]->nom_specialite,
					'comp_nom_ecole' . $i => $formation[$r]->nom_ecole,
					'comp_niveau_diplome' . $i => $formation[$r]->libelle_niveau_etude,
					'comp_date_debut_formation' . $i => $formation[$r]->date_debut_formation,
					'comp_date_fin_formation' . $i => $formation[$r]->date_fin_formation
				);
				//$key = array("identifiant_personne" => $this->su[0]->id_utilisateur);
				$key = array("identifiant_personne" => $userId);
				$updateFormation = $this->model->update_data($this->gestion, $info_etude, $key);
			}
		}
	}

    /**
     * Association
     * @return ResponseInterface
     */
	public function save_association(): ResponseInterface
	{
         $userId = $this->request->userId ?? null;
        $request = $this->request->getJSON(true);

		//Vérification si la case "Aucune Association" est coché
		if (!$request["no-association"]) {
			$activiteAssocie = $request['activite_associe'];
			$posteOccupe = $request['poste_occupe'];

			//Debut
			$jourDebut = $request['jourFormDebut'];
			$moisDebut = $request['moisFormDebut'];
			$anneeDebut = $request['anneeFormDebut'];

			//Vérification de la validité de la date
			$debutEmploi = $jourDebut . '/' . $moisDebut . '/' . $anneeDebut;

			//Fin
			$jourFin = $request['jourFormFin'];
			$moisFin = $request['moisFormFin'];
			$anneeFin = $request['anneeFormFin'];

			//Vérification de la validité de la date
			$finEmploi = $jourFin . '/' . $moisFin . '/' . $anneeFin;

			$typeAssociation = $request['type_association'];
			$nomAssociation = $request['nom_association'];

			if ($anneeDebut > $anneeFin) {
				$data = $this->msgError . " : " . "les dates ne concordent pas.";
				return $this->response
					->setStatusCode(422)
					->setJSON(json_encode([
						'status' => 'error',
						'message' => $data,
					]));
			} elseif ($anneeDebut == $anneeFin) {
				if ($moisDebut > $moisFin) {
					$data = $this->msgError . " : " . "les dates ne concordent pas.";
					return $this->response
						->setStatusCode(422)
						->setJSON(json_encode([
							'status' => 'error',
							'message' => $data,
						]));
				} elseif ($moisDebut == $moisFin) {
					if ($jourDebut > $jourFin) {
						$data = $this->msgError . " : " . "les dates ne concordent pas.";
						return $this->response
							->setStatusCode(422)
							->setJSON(json_encode([
								'status' => 'error',
								'message' => $data,
							]));
					}
				}
			}

			//Eléments de sauvegarde
			$association = array(
				'activite_associe' => $activiteAssocie,
				'poste_occupe' => $posteOccupe,
				'debut_association' => $debutEmploi,
				'fin_association' => $finEmploi,
				'type_association' => $typeAssociation,
				'nom_association' => $nomAssociation,
				'date_create_associe' => time(),
				'utilisateur_associe' => $userId
			);
		}

		try {
			$insertAssociation = $this->model->insert_data($this->associe, $association);

			$data = $this->msgSuccess;
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'message' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}

	 /**
     * Association
     * @return ResponseInterface
     */
	public function update_association($id): ResponseInterface
	{
        $userId = $this->request->userId ?? null;
        $request = $this->request->getJSON(true);

		//Vérification si la case 'Aucune Association' est coché
		if (!$request["no-association"]) {
			$activiteAssocie = $request['activite_associe'];
			$posteOccupe = $request['poste_occupe'];

			//Debut
			$jourDebut = $request['jourFormDebut'];
			$moisDebut = $request['moisFormDebut'];
			$anneeDebut = $request['anneeFormDebut'];

			//Vérification de la validité de la date
			$debutEmploi = $jourDebut . '/' . $moisDebut . '/' . $anneeDebut;

			//Fin
			$jourFin = $request['jourFormFin'];
			$moisFin = $request['moisFormFin'];
			$anneeFin = $request['anneeFormFin'];

			//Vérification de la validité de la date
			$finEmploi = $jourFin . '/' . $moisFin . '/' . $anneeFin;

			$typeAssociation = $request['type_association'];
			$nomAssociation = $request['nom_association'];

			if ($anneeDebut > $anneeFin) {
				$data = $this->msgError . " : " . "les dates ne concordent pas.";
				return $this->response
					->setStatusCode(422)
					->setJSON(json_encode([
						'status' => 'error',
						'message' => $data,
					]));
			} elseif ($anneeDebut == $anneeFin) {
				if ($moisDebut > $moisFin) {
					$data = $this->msgError . " : " . "les dates ne concordent pas.";
					return $this->response
						->setStatusCode(422)
						->setJSON(json_encode([
							'status' => 'error',
							'message' => $data,
						]));
				} elseif ($moisDebut == $moisFin) {
					if ($jourDebut > $jourFin) {
						$data = $this->msgError . " : " . "les dates ne concordent pas.";
						return $this->response
							->setStatusCode(422)
							->setJSON(json_encode([
								'status' => 'error',
								'message' => $data,
							]));
					}
				}
			}

			//Eléments de sauvegarde
			$association = array(
				'activite_associe' => $activiteAssocie,
				'poste_occupe' => $posteOccupe,
				'debut_association' => $debutEmploi,
				'fin_association' => $finEmploi,
				'type_association' => $typeAssociation,
				'nom_association' => $nomAssociation,
				'date_update_associe' => time(),
				'utilisateur_associe' => $userId
			);
		}

		try {
			$key = array('id_activete_association' => $id);
			$insertAssociation = $this->model->update_data($this->associe, $association, $key);

			$data = $this->msgSuccess;
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'message' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}

	/**
     * Association
     * @return ResponseInterface
     */
	public function delete_association($id): ResponseInterface
	{
		if (is_null($id)) {
			$data = $this->msgError;
			return $this->response
				->setStatusCode(422)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}

		try {
			$key = array('id_activete_association' => $id);
			$deleteAssocie = $this->model->delete_one_data($this->associe, $key);

			$data = $this->msgSuccess;
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'message' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}

	/**
	 * Fonction de recupération de la liste
	 * Association
	 **/
	public function getAll_associations()
	{
        $userId = $this->request->userId ?? null;
        $request = $this->request->getJSON(true);

		$clause_where = array('utilisateur_associe' => $userId);
		try {
			$data = $this->model->select_data($this->associe, $clause_where);
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'association' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}

    /**
     * Attestation
     * @return ResponseInterface
     */
    public function save_attestation(): ResponseInterface
	{
        $userId = $this->request->userId ?? null;
        $request = $this->request->getJSON(true);

		//Vérification si la case "Aucune Attestation" a été cochée 
		if (!$request['no-attestation']) {
			$nomAttestation = $request['nom_attestation'];
			$referenceAttestation = $request['reference_attestation'];
			$typeAttestation = $request['type_attestation'];

			//Debut
			$jourDebut = $request['jourFormDebut'];
			$moisDebut = $request['moisFormDebut'];
			$anneeDebut = $request['anneeFormDebut'];

			//Vérification de la validité de la date
			$debutEmploi = $jourDebut . '/' . $moisDebut . '/' . $anneeDebut;

			//date du jour
			$j = date('j');
			$m = date('n');
			$a = date('Y');

			$date_day = $j . "/" . $m . "/" . $a;

			if (($a < $anneeDebut) || (($a == $anneeDebut) && ($m < $moisDebut)) || (($a == $anneeDebut) && ($m == $moisDebut) && ($j < $jourDebut))) {
				$data = $this->msgError . " : " . "les dates ne concordent pas.";
				return $this->response
					->setStatusCode(422)
					->setJSON(json_encode([
						'status' => 'error',
						'message' => $data,
					]));
			}

			//Fin
			$jourFin = $request['jourFormFin'];
			$moisFin = $request['moisFormFin'];
			$anneeFin = $request['anneeFormFin'];

			//Vérification de la validité de la date
			$finEmploi = $jourFin . '/' . $moisFin . '/' . $anneeFin;

			$organeEmetteur = $request['organe_emetteur'];
			$paysObtention = $request['pays_obtention'];

			if ($anneeDebut > $anneeFin) {
				$data = $this->msgError . " : " . "les dates ne concordent pas.";
				return $this->response
					->setStatusCode(422)
					->setJSON(json_encode([
						'status' => 'error',
						'message' => $data,
					]));
			} elseif ($anneeDebut == $anneeFin) {
				if ($moisDebut > $moisFin) {
					$data = $this->msgError . " : " . "les dates ne concordent pas.";
					return $this->response
						->setStatusCode(422)
						->setJSON(json_encode([
							'status' => 'error',
							'message' => $data,
						]));
				} elseif ($moisDebut == $moisFin) {
					if ($jourDebut > $jourFin) {
						$data = $this->msgError . " : " . "les dates ne concordent pas.";
						return $this->response
							->setStatusCode(422)
							->setJSON(json_encode([
								'status' => 'error',
								'message' => $data,
							]));
					}
				}
			}

			//Eléments de sauvegarde
			$attestation = array(
				'nom_attestation' => $nomAttestation,
				'reference_attestation' => $referenceAttestation,
				'type_attestation' => $typeAttestation,
				'date_emission' => $debutEmploi,
				'date_expiration' => $finEmploi,
				'pays_obtention' => $paysObtention,
				'organe_emetteur' => $organeEmetteur,
				'date_create_attestation' => time(),
				'utilisateur_attestation' => $userId
			);
		}

		try {
			$insertAttestation = $this->model->insert_data($this->atteste, $attestation);

			$data = $this->msgSuccess;
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'message' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}

     /**
     * Attestation
     * @return ResponseInterface
     */
    public function update_attestation($id): ResponseInterface
	{
        $userId = $this->request->userId ?? null;
        $request = $this->request->getJSON(true);

		//Vérification si la case "Aucune Attestation" a été cochée
		if (!$request['no-attestation']) {
			$nomAttestation = $request['nom_attestation'];
			$referenceAttestation = $request['reference_attestation'];
			$typeAttestation = $request['type_attestation'];

			//Debut
			$jourDebut = $request['jourFormDebut'];
			$moisDebut = $request['moisFormDebut'];
			$anneeDebut = $request['anneeFormDebut'];

			//$valideDateFin = $this->Utilisateur_model->valide_date($jourDebut, $moisDebut, $anneeDebut, $redirectLink);
			$debutEmploi = $jourDebut . '/' . $moisDebut . '/' . $anneeDebut;

			//date du jour
			$j = date('j');
			$m = date('n');
			$a = date('Y');

			$date_day = $j . "/" . $m . "/" . $a;

			if (($a < $anneeDebut) || (($a == $anneeDebut) && ($m < $moisDebut)) || (($a == $anneeDebut) && ($m == $moisDebut) && ($j < $jourDebut))) {
				$data = $this->msgError . " : " . "la date de début  (" . $debutEmploi . ") ne peut pas être suppérieur à aujourd'hui " . date('d/m/Y');
				return $this->response
					->setStatusCode(422)
					->setJSON(json_encode([
						'status' => 'error',
						'message' => $data,
					]));
			}

			//Fin
			$jourFin = $request['jourFormFin'];
			$moisFin = $request['moisFormFin'];
			$anneeFin = $request['anneeFormFin'];

			//Vérification de la validité de la date
			$finEmploi = $jourFin . '/' . $moisFin . '/' . $anneeFin;

			$organeEmetteur = $request['organe_emetteur'];
			$paysObtention = $request['pays_obtention'];

			if ($anneeDebut > $anneeFin) {
				$data = $this->msgError . " : " . "les dates ne concordent pas.";
				return $this->response
					->setStatusCode(422)
					->setJSON(json_encode([
						'status' => 'error',
						'message' => $data,
					]));
			} elseif ($anneeDebut == $anneeFin) {
				if ($moisDebut > $moisFin) {
					$data = $this->msgError . " : " . "les dates ne concordent pas.";
					return $this->response
						->setStatusCode(422)
						->setJSON(json_encode([
							'status' => 'error',
							'message' => $data,
						]));
				} elseif ($moisDebut == $moisFin) {
					if ($jourDebut > $jourFin) {
						$data = $this->msgError . " : " . "les dates ne concordent pas.";
						return $this->response
							->setStatusCode(422)
							->setJSON(json_encode([
								'status' => 'error',
								'message' => $data,
							]));
					}
				}
			}

			//Eléments de sauvegarde
			$attestation = array(
				'nom_attestation' => $nomAttestation,
				'reference_attestation' => $referenceAttestation,
				'type_attestation' => $typeAttestation,
				'date_emission' => $debutEmploi,
				'date_expiration' => $finEmploi,
				'pays_obtention' => $paysObtention,
				'organe_emetteur' => $organeEmetteur,
				'date_update_attestation' => time(),
				'utilisateur_attestation' => $userId
			);
		}

		try {
			$key = array('id_attestation' => $id);
			$val_key = $id;
			$updateAttestation = $this->model->update_data($this->atteste, $attestation, $key);

			$data = $this->msgSuccess;
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'message' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}

    /**
     * Attestation
     * @return ResponseInterface
     */
    public function delete_attestation($id): ResponseInterface
	{
		if (is_null($id)) {
			$data = $this->msgError;
			return $this->response->setJSON(json_encode($data));
		}

		try {
			$key = array('id_attestation' => $id);
			$deleteExpert = $this->model->delete_one_data($this->atteste, $key);

			$data = $this->msgSuccess;
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'message' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}

    /**
	 * Fonction de recupération de la liste
	 * Attestation
	 **/
    public function getAll_attestations()
	{
        $userId = $this->request->userId ?? null;
        $request = $this->request->getJSON(true);
		$clause_where = array('utilisateur_attestation' => $userId);
		try {
			$data = $this->model->select_data($this->atteste, $clause_where);
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'association' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}

     /**
     * Compétences
     * @return ResponseInterface
     */
    public function save_competence(): ResponseInterface
	{
		$userId = $this->request->userId ?? null;   
        $request = $this->request->getJSON(true);

		//Vérification qu'il a coché la case aucune compétence
		if (!$request['no-skill']) {
			$name = $request['nom'];
			$niveau = $request['niveau'];
			$commentaire = $request['commentaire'];

			//Vérification des champs 
			if ($name && $niveau) {
				//Eléments de sauvegarde de la compétence
				$competence = array(
					'nom_competence' => $name,
					'niveau_competence' => $niveau,
					'commentaire_competence' => $commentaire,
					'utilisateur_competence' => $userId
				);
			} else {
				$data = "Veuillez renseigner le nom et/ou le niveau de votre compétence!";
				return $this->response
					->setStatusCode(422)
					->setJSON(json_encode([
						'status' => 'error',
						'message' => $data,
					]));
			}
		}


		try {
			$insertCompetence = $this->model->insert_data($this->compet, $competence);

			$data = $this->msgSuccess;
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'message' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}

    /**
     * Compétences
     * @return ResponseInterface
     */
    public function update_competence($id): ResponseInterface
	{
		 $request = $this->request->getJSON(true);
         
		if (!$request['no-skill']) {
			$name = $request['nom'];
			$niveau = $request['niveau'];
			$commentaire = $request['commentaire'];

			if ($name && $niveau) {
				//Eléments de sauvegarde de la compétence
				$competence = array(
					'nom_competence' => $name,
					'niveau_competence' => $niveau,
					'commentaire_competence' => $commentaire,
				);
			} else {
				$data = "Veuillez renseigner le nom et/ou le niveau de votre compétence!";
				return $this->response
					->setStatusCode(422)
					->setJSON(json_encode([
						'status' => 'error',
						'message' => $data,
					]));
			}
		}

		try {
			$key = array('id_competence' => $id);
			$updateCompet = $this->model->update_data($this->compet, $competence, $key);

			$data = $this->msgSuccess;
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'message' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}

    /**
     * Compétences
     * @return ResponseInterface
     */
    public function delete_competence($id): ResponseInterface
	{
		if (is_null($id)) {
			$data = $this->msgError;
			return $this->response
				->setStatusCode(422)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}

		try {
			$key = array('id_competence' => $id);
			$deleteExpert = $this->model->delete_one_data($this->compet, $key);

			$data = $this->msgSuccess;
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'message' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}

    /**
	 * Fonction de recupération de la liste 
	 * Compétence
	 */
	public function getAll_competences()
	{
        $userId = $this->request->userId ?? null;
        $request = $this->request->getJSON(true);

		$clause_where = array('utilisateur_competence' =>  $userId);
		try {
			$data = $this->model->select_data($this->compet, $clause_where);
			return $this->response
				->setStatusCode(200)
				->setJSON(json_encode([
					'status' => 'success',
					'competence' => $data,
				]));
		} catch (Exception $e) {
			$data = $this->msgError . " : " . $e->getMessage();
			return $this->response
				->setStatusCode(400)
				->setJSON(json_encode([
					'status' => 'error',
					'message' => $data,
				]));
		}
	}
}
