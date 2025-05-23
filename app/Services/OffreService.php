<?php

namespace App\Services;

use App\Models\OffreModel;
use App\Models\OffreSouscriptionModel;
use App\Models\OffreVModel;
use App\Models\SessionModel;
use App\Models\UserExternModel;

class OffreService
{
    protected OffreModel $offreModel;

    public function __construct()
    {
        $this->offreModel = new OffreModel();
    }

    /**
     * Récupère toutes les offres actives depuis la vue `v_offres_active`.
     */
    public function getAllActive(): array
    {
        $model = new OffreModel();
        $model->setTable($model->v_offres_active); // utiliser la vue au lieu de la table
        return $model->findAll();
    }

    /**
     * Récupère les 3 dernières offres (triées par date descendante si applicable).
     */
    public function getLast(int $limit = 3): array
    {
        $model = new OffreModel();
        $model->setTable($model->v_offres); // utiliser la vue au lieu de la table
        return $model->findAll($limit);
    }


    /**
     * Récupère toutes les offres depuis la table `t_offre_emplois`.
     */
    public function getAll(): array
    {
        return $this->offreModel->findAll();
    }


    public function getById(int $id, array $filters = []): array | null
    {
        $model = new OffreModel();
        $model->setTable($model->v_offres); // utiliser la vue au lieu de la table
        $model = $model->where($model->primaryKey, $id);

        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                $model = $model->whereIn($key, $value);
            } else {
                $model = $model->where($key, $value);
            }
        }
        return $model->first();
    }

    public function getBy(array $filters = [], ?string $table=null): array
    {
        $model = new OffreModel();

        // Déterminer dynamiquement la table à utiliser
        $availableViews = [
            'v_offres_souscrite' => $model->v_offres_souscrite,
            'v_offres' => $model->v_offres,
            'v_offres_active' => $model->v_offres_active,
        ];

        // Si la table demandée existe dans la config, on la set, sinon on garde celle par défaut
        if (array_key_exists($table, $availableViews)) {
            $model->setTable($availableViews[$table]);
        }


        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                // Si c'est un tableau, on utilise whereIn
                $model = $model->whereIn($key, $value);
            } else {
                $model = $model->where($key, $value);
            }
        }

        return $model->findAll();
    }

    /**
     * Creation d'une offre dans `t_offre_emplois`.
     */
    public function create(array $data = []): array
    {
        $result = $this->offreModel->save($data);
        if ($result) {
            // Assuming the primary key is 'id'
            $id = $this->offreModel->insertID ?? null;
            if ($id) {
                return $this->offreModel->find($id);
            }
            // If insertID is not available, return the data as is
            return $data;
        }
        // On failure, return an empty array or handle error as needed
        return [];
    }

     /**
     * Souscription à une offre dans `t_souscription_offres`.
     */
    public function souscription(array $data = []): array | object | null
    {
        $model = new OffreSouscriptionModel();
        $result = $model->save($data);
        if ($result) {
            // Assuming the primary key is 'id'
            $id = $model->insertID ?? null;
            if ($id) {
                return $model->find($id);
            }
            // If insertID is not available, return the data as is
            return $data;
        }
        // On failure, return an empty array or handle error as needed
        return null;
    }
}
