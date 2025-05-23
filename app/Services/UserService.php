<?php

namespace App\Services;

use App\Models\SessionModel;
use App\Models\UserExternModel;
use App\Models\UserModel;

class UserService
{
    public function getActiveUser(string $email): array
    {
        $userModel = new UserModel();
        return $userModel
            ->where('user_actif', 1)
            ->where('utilisateur', $email)->first();
    }

    public function getUserById(string $id): array | null
    {
        $model = new UserExternModel();
        return $model
           ->select("id_utilisateur ,utilisateur as email , nom ,prenom,date_naissance,genre,image,fonction,lieu_naissance_use,pays_origine,pays_naissance,lieu_naissance,region_origine,departement_naissance,arrondissement_origine,region_naissance,ville,adresse_1,adresse_2,telephone_1,telephone_2")
            ->where('user_actif', 1)
            ->where('id_utilisateur', $id)->first();
    }

        public function getUserByEmail(string $email): array  | null
    {
        $model = new UserModel();
        return $model
            ->where('user_actif', 1)
            ->where('utilisateur', $email)->first();
    }

    public function setLastConnexion(int $userId): bool
    {
        $model = new UserModel();

        if (!$userId) {
            return false;
        }

        return  $model
            ->update($userId, ['last_connexion' => date('Y-m-d H:i:s')]);
    }
}
