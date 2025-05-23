<?php

namespace App\Controllers\Api;

use App\Models\InfoMailModel;
use App\Models\UserModel;
use App\Services\OffreService;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class OfferController extends ResourceController
{
    protected $format = 'json';
    protected OffreService $offreService;
    protected $sendMail;


    public function __construct()
    {
        $this->offreService = new OffreService();
        $this->sendMail = new InfoMailModel();
    }

    /**
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return $this->respond($this->offreService->getAll(), 200);
    }

    /**
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null): ResponseInterface
    {
        $data = $this->offreService->getById($id);
        if (!$data) {
            return $this->failNotFound();
        }
        return $this->respond($data);
    }


    /**
     *
     * @return ResponseInterface
     */
    public function create(): ResponseInterface
    {
        $this->model->insert([
            'name' => esc($this->request->getVar('name')),
        ]);
        return $this->respondCreated([
            'message' => 'CORS fonctionne bien 👌',
            'status' => 201,
            'data' => $this->model->findAll()
        ]);
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        //
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        //
    }

    /**
     * 
     * @return ResponseInterface
     */
    public function active(): ResponseInterface
    {
        return $this->respond($this->offreService->getAllActive(), 200);
    }


    /**
     * 
     * @return ResponseInterface
     */
    public function last(): ResponseInterface
    {
        return $this->respond($this->offreService->getLast(), 200);
    }


    /**
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function apply($id = null): ResponseInterface
    {
        $userId = $this->request->userId ?? null;
        if (!$userId) {
            return $this->failUnauthorized(lang('message.msg_user_not_connected'));
        }

        if (!$id) {
            return $this->failForbidden(lang('message.msg_requete_none'));
        }


        $offre_a_souscrire = $this->offreService->getById($id, [
            "statut_offre" => '1'
        ]);
        if (!$offre_a_souscrire) {
            return $this->failNotFound(lang('message.offre_non_dispo'));
        }

        // Vérification de la date de fin de l'offre
        $ceJour = strtotime(date("m/d/Y"));
        $endDataEmploi = strtotime($offre_a_souscrire['date_fin_valide']);
        if ($endDataEmploi < $ceJour)
        {
            return $this->failForbidden(lang('message.offre_non_dispo'));
        }


        $data = $this->offreService->getBy([
            'offre' => $id,
            'utilisateur_souscripteur' =>  $userId
        ], 'v_offres_souscrite');
        $existe = count($data) > 0;

        if ($existe) {
            return $this->failForbidden(lang('message.msg_deja_souscris'));
        }

        //Eléments de sauvegarde de la souscription
        $souscription = array(
            'offre' => $id,
            'date_souscription' => time(),
            'utilisateur_souscripteur' => $userId
        );

        try {
            $insertSouscription = $this->offreService->souscription($souscription);
            if (!$insertSouscription) {
                return $this->fail(lang('message.msg_souscription_error'));
            }

            $souscriUser =  $this->offreService->getBy([
                'offre' => $id,
                'utilisateur_souscripteur' =>  $userId
            ], 'v_offres_souscrite');

            if (!$souscriUser) {
                return $this->fail(lang('message.msg_souscription_error'));
            }
            $titre = $souscriUser[0]['titre_offre'];
            $code = $souscriUser[0]['code_offre'];

            // TODO  Mail d'information
            // $user = (new UserModel())->where('id_utilisateur', $userId)->first();
            // $mail_info = $user['utilisateur'];
            // $objet_message_info = lang('message.mail_objet_souscription') . $titre . " (Ref : " . $code . ")";
            // $message_info = lang('message.mail_souscription_offre');
            // $this->sendMail->envoi_mail_postule($mail_info, $message_info, $objet_message_info);

            return $this->respondCreated([
                'message' =>  lang('message.msg_souscription_done'),
                'data' => $this->offreService->getBy([
                    'utilisateur_souscripteur' =>  $userId
                ], 'v_offres_souscrite')
            ], 200);
        } catch (\Exception $e) {
            return $this->fail([
                'message' => lang('message.error') . " : " . $e->getMessage(),
                'status' => 500,
                'error' => []
            ], 500);
        }
    }


    /**
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function my(): ResponseInterface
    {
        $userId = $this->request->userId ?? null;

        $data = $this->offreService->getBy([
            'utilisateur_souscripteur' =>  $userId
        ], 'v_offres_souscrite');

        return $this->respond($data, 200);
    }
}
