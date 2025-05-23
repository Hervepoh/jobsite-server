<?php

namespace App\Services;

use App\Models\UserExternModel;
use App\Models\UserModel;

class UserService
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function getActiveUser(string $email): array | object | null
    {
        return $this->userModel
            ->where('user_actif', '1')
            ->where('utilisateur', $email)
            ->first();
    }

    public function getUserById(string $id): array  | object | null
    {
        $model = new UserExternModel();
        return $model
            ->select("id_utilisateur ,utilisateur as email , nom ,prenom,date_naissance,genre,image,fonction,lieu_naissance_use,pays_origine,pays_naissance,lieu_naissance,region_origine,departement_naissance,arrondissement_origine,region_naissance,ville,adresse_1,adresse_2,telephone_1,telephone_2")
            ->where('user_actif', '1')
            ->where('id_utilisateur', $id)->first();
    }

    public function getUserByEmail(string $email): array  | object  | null
    {
        return $this->userModel
            ->where('user_actif', '1')
            ->where('utilisateur', $email)->first();
    }

    public function setLastConnexion(int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return  $this->userModel
            ->update($userId, ['last_connexion' => date('Y-m-d H:i:s')]);
    }

    /**
     * Creation d'une offre dans `t_offre_emplois`.
     */
    public function create(array $data = []): object | null
    {
        $result = $this->userModel->save($data);
        if ($result) {
            // Assuming the primary key is 'id'
            $id = $this->userModel->insertID ?? null;
            if ($id) {
                return $this->userModel->find($id);
            }
            // If insertID is not available, return the data as is
            return null;
        }
        // On failure, return an empty array or handle error as needed
        return null;
    }
}
