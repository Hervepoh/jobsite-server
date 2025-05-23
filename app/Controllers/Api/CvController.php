<?php

namespace App\Controllers\Api;

use App\Models\DataModel;
use App\Models\InfoMailModel;
use App\Models\UserModel;
use App\Services\OffreService;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use \Exception;

class CvController extends ResourceController
{
    protected $format = 'json';
    protected OffreService $offreService;

    private $lang = 't_langues';

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
     *
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
            $langue = array(
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
            $insertLangue = $this->model->insert_data($this->lang, $langue);
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
     *
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
     *
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
}
