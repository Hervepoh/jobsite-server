<?php

namespace App\Controllers\Api;

use App\Models\AddressModel;
use App\Models\DataModel;
use App\Models\InfoMailModel;
use App\Models\PersonModel;
use App\Models\SessionModel;
use App\Models\UploadModel;
use App\Models\UserModel;
use App\Services\SessionService;
use App\Services\UserService;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use \Exception;


class AuthController extends ResourceController
{
    protected $format = 'json';

    protected UserService $userService;
    protected $model = null;
    protected $fichier = null;
    protected $sendMail = null;


    public function __construct()
    {
        $this->model = new DataModel();
        $this->sendMail = new InfoMailModel();
        $this->fichier = new UploadModel();
        $this->userService = new UserService();
    }


    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function login()
    {
        try {
            helper('\App\Helpers\Jwt');

            $validation = service('validation');
            $validation->setRules([
                'email' => 'required|valid_email',
                'password' => 'required'
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return $this->failValidationErrors($validation->getErrors());
            }

            $request = $this->request->getJSON(true);
            $loginUser = trim($request['email']);
            $passUser =  trim($request['password']);
            $userAgent = $request['userAgent'] ?? $this->request->getUserAgent();
            $ipAddress = $this->request->getIPAddress();

            $user = $this->userService->getActiveUser($loginUser);

            if (!$user) {
                log_message('warning', "Login failed for email: {$loginUser}");
                return $this->failUnauthorized('Invalid email or password');
            }

            // TODO: Reactiver password_verify en prod
            // if (!password_verify($password, $user['passe'])) {
            //     return $this->failUnauthorized('Invalid email or password');
            // }

            // Vérifier si 2FA est activé
            if (property_exists($user, 'enable2FA') && $user->enable2FA) {
                log_message('info', "2FA required for user ID: {$user->id}");
                return $this->response->setJSON([
                    'user' => null,
                    'mfaRequired' => true,
                    'accessToken' => '',
                    'refreshToken' => ''
                ]);
            }

            $jwtAccessTokenExpires = env('JWT_ACCESS_TOKEN_EXPIRES', 3600); // 1h
            $jwtRefreshTokenExpires = env('JWT_REFRESH_TOKEN_EXPIRES', 604800); // 30jr


            // Vérifier si une session active existe déjà pour cet utilisateur et cette IP
            $sessionModel = new SessionModel();
            $existingSession = $sessionModel
                ->where('user_id', $user->id_utilisateur)
                ->where('ip_address', $ipAddress)
                ->where('user_agent', $userAgent)
                ->where('active', 1)
                ->first();


            // if ($existingSession) {
            //     // Si session existe déjà, renvoyer les infos existantes
            //     log_message('info', "Session already exists for user ID: {$user['id_utilisateur']}");
            //     $accessToken = generate_jwt([
            //         'userId' => $existingSession->user_id,
            //         'sessionId' => $existingSession->id,
            //     ], $jwtAccessTokenExpires);

            //     return $this->respondWithTokens(
            //         $user,
            //         $accessToken,
            //         $existingSession->refresh_token,
            //         $jwtAccessTokenExpires,
            //         $existingSession->expires_at
            //     );
            // }

            // Générer nouvel ID de session
            $sessionModel = new SessionModel();
            $sessionId =  env('session.apiName', 'jobsite') . ':' . bin2hex(random_bytes(32));

            // Générer des tokens JWT
            $accessToken = generate_jwt([
                'userId' => $user->id_utilisateur,
                'sessionId' => $sessionId,
            ], $jwtAccessTokenExpires);

            $refreshToken = generate_jwt([
                'sessionId' => $sessionId,
            ], $jwtRefreshTokenExpires);

            $created =  $sessionModel->insert([
                'id' =>  $sessionId,
                'user_id' => $user->id_utilisateur,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'refresh_token' => $refreshToken,
                'expires_at' => time() + $jwtRefreshTokenExpires,
                'timestamp' => time(),
                'active' => 1,
            ], true);

            if (!$created) {
                log_message('error', "Failed to create session for user ID: {$user->id_utilisateur}");
                return $this->failServerError('Failed to create session');
            }

            log_message('info', "Login successful for user ID: {$user->id_utilisateur}");
            $this->userService->setLastConnexion($user->id_utilisateur);

            return $this->respondWithTokens(
                $user,
                $accessToken,
                $refreshToken,
                $jwtAccessTokenExpires,
                $jwtRefreshTokenExpires
            );
        } catch (Exception $e) {
            return $this->failServerError($e);
        }
    }


    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function register()
    {
        helper('\App\Helpers\Jwt');

        $validation = service('validation');
        /* The above code is setting validation rules for various input fields in a PHP application.
       Each field has specific rules defined using the setRules method. Here are the rules for each
       field: */
        /* The above PHP code is setting validation rules for a form input fields using a validation
       library or class. Each field in the form is being validated based on certain rules: */
        $validation->setRules([
            'name' => 'required|min_length[1]|max_length[200]|trim',
            'surname' => 'required|min_length[1]|max_length[200]|trim',
            'genre' => 'required|min_length[1]|max_length[1]|trim',
            'phone' => 'required|min_length[1]|max_length[200]|trim',
            'mail' => 'required|trim',
            'user_pass' => 'required|min_length[8]|trim',
            'confirm_user_pass' => 'required|min_length[8]|trim'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'errors' => $validation->getErrors()
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $request = $this->request->getJSON(true);

        $fonction = $request['activite'] ?? null;
        $name =  $request['name'];
        $surname =  $request['surname'];
        $genre =  $request['genre'];
        $phone = $request['phone'];
        $phone2 = $request['phone2'] ?? null;
        $code_phone = $request['codePays'] ?? null;
        $code_phone2 = $request['codePays2'] ?? null;
        $mail = strtolower(trim($request['mail'])) ?? null;
        $userPass = $request['user_pass'] ?? null;
        $confirmUserPass = $request['confirm_user_pass'] ?? null;
        $paysOrigine = $request['paysOrigine'] ?? null;
        $paysNaissance = $request['nationalite'] ?? null;
        $departement_naisse = $request['departement_naisse'] ?? null;
        $regionOrigine = $request['region_origine'] ?? null;
        $arrondiOrigine = $request['arrondissement_origine'] ?? null;
        $lieu = $request['lieu_naissance_use'] ?? null;
        $vil = $request['lieu_naisse'] ?? null;
        $jour = $request['jour'] ?? null;
        $mois = $request['mois'] ?? null;
        $annee = $request['annee'] ?? null;

        //Définition de la date
        $anneeNaissance = $jour . '/' . $mois . '/' . $annee;

        $mon_age = date('Y') - $annee;
        // Vérifie si l'utilisateur a l'age légal
        if ($mon_age < 17) {
            $data = lang('message.error_legacy_born');
            return $this->failValidationErrors($data);
        }

        // Vérifie si l'addresse mail est au bon format
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $data = lang('message.error_mail');
            return $this->failValidationErrors($data);
        }

        if ($confirmUserPass != $userPass) {
            $data = lang('message.error_pwd_dif');
            return $this->failValidationErrors($data);
        }

        $mailCheck =  $this->userService->getUserByEmail($mail);

        if (isset($mailCheck) && !empty($mailCheck)) {
            $data = lang('message.error_mail_use');
            return $this->response->setJSON(json_encode($data));
        }

        $resultatComplexite = $this->model->complexite_password($confirmUserPass);

        if (is_numeric($resultatComplexite) && $resultatComplexite < 19) {
            $data = lang('message.error_pwd_faible');
            return $this->failValidationErrors($data);
        }

        $passUse = $this->model->crypt_password($confirmUserPass);

        $adresse = array(
            'adresse_mail' => $mail,
            'telephone_1' => $code_phone . "-" . $phone,
            'telephone_2' => $code_phone2 . "-" . $phone2
        );

        $addressModel = new AddressModel();
        $insertAdresse = $addressModel->insert($adresse, true);

        if ($insertAdresse == null || $insertAdresse == 0) {
            $data = lang('message.msg_info_rejet');
            return $this->response->setJSON(json_encode($data));
        }

        // $img = $this->request->getFile('piece_media');
        // $insertDocument = $this->fichier->upload($img);
        //Eléments de sauvegarde de la personne
        $personne = array(
            'nom' => $name,
            'prenom' => $surname,
            'date_naissance' => $anneeNaissance,
            'pays_origine' => $paysOrigine,
            'pays_naissance' => $paysNaissance,
            'lieu_naissance_use' => $lieu,
            'region_origine' => $regionOrigine,
            'departement_naissance' => $departement_naisse,
            'arrondissement_origine' => $arrondiOrigine,
            /*'region_naissance' => $regionNaissance, */
            'ville' => $vil,
            'fonction' => $fonction,
            'genre' => $genre,
            'adresse' => $insertAdresse,
            //'image' => $insertDocument,
            'date_create_personne' => time()
        );

        $insertPersonne = (new PersonModel())->insert($personne, true);

        if ($insertPersonne == null || $insertPersonne == 0) {
            $data = lang('message.msg_info_rejet');
            return $this->response->setJSON(json_encode($data));
        } else {

            //Eléments de l'utilisateur
            $user = array(
                "utilisateur" => $mail,
                "passe" => $passUse,
                "personne" => $insertPersonne,
                "role" => 3,
                "groupe" => 3
            );

            try {
                //Sauvegarde de la personne
                $insertUser = (new UserModel())->save($user);
                $email = $mail;
                $nom = $name;
                $prenom = $surname;

                //$this->sendMail->envoi_mail_compte_cree($email, $nom, $prenom);

                $data = "done";
                return $this->response->setJSON(json_encode($data));
            } catch (Exception $e) {

                $data = lang('message.error') . " : " . $e->getMessage();
                return $this->response->setJSON(json_encode($data));
            }
        }
    }


    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function refresh()
    {
        helper('\App\Helpers\Jwt');

        $refreshToken = $this->request->getCookie('refreshToken');

        if (!$refreshToken) {
            return $this->failUnauthorized('Refresh token not provided');
        }

        try {
            $payload = decode_jwt($refreshToken);

            if (!$payload) {
                return $this->failUnauthorized('Invalid refresh token');
            }

            $sessionId = $payload->sessionId ?? null;
            if (!$sessionId) {
                throw new \Exception('Invalid token payload');
            }

            $sessionModel = new SessionModel();
            // $session = $sessionModel->where('id_session', $sessionId)->first();
            // if (!$session) {
            //     return $this->failUnauthorized('Session does not exist');
            // }

            $jwtAccessTokenExpires = env('JWT_ACCESS_TOKEN_EXPIRES', 3600); // 1h

            // Génère un nouveau accessToken
            $accessToken = generate_jwt([
                //'userId' => $session['id_utilisateur'],
                'sessionId' => $sessionId,
            ], $jwtAccessTokenExpires);

            // Ajoute dans un cookie HttpOnly
            $isSecure = $_ENV['CI_ENVIRONMENT'] === 'production';

            $this->response->setCookie([
                'name' => 'accessToken',
                'value' => $accessToken,
                'expire' => $jwtAccessTokenExpires,
                'httponly' => true,
                'secure' => $isSecure,
                'samesite' => $isSecure ? 'None' : 'Lax',
                'path' => '/',
            ]);

            return $this->response->setJSON([
                'message' => 'Access token refreshed',
            ]);
        } catch (\Exception $e) {
            return $this->response
                ->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED)
                ->setJSON(['error' => 'Invalid or expired refresh token']);
        }
    }


    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function logout()
    {
        // $refreshToken = $this->request->getCookie('refreshToken');
        $sessionId = $this->request->sessionId ?? null;
        $userId = $this->request->userId ?? null;

        if ($sessionId && $userId) {
            (new SessionService())->deleteSession($sessionId, $userId);
        }

        // Supprime les cookies
        $this->response->deleteCookie('accessToken');
        $this->response->deleteCookie('refreshToken');

        return $this->response->setJSON(['message' => 'Logout successful']);
    }


    // Méthode auxiliaire pour envoyer réponse + cookies
    protected function respondWithTokens(array | object $user, string $accessToken, string $refreshToken, int $accessExpire, int $refreshExpire)
    {
        $isSecure = env('CI_ENVIRONMENT') === 'production';

        $this->response->setCookie([
            'name'     => 'accessToken',
            'value'    => $accessToken,
            'expire'   => $accessExpire,
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => $isSecure ? 'None' : 'Lax',
        ]);

        $this->response->setCookie([
            'name'     => 'refreshToken',
            'value'    => $refreshToken,
            'expire'   => $refreshExpire,
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => $isSecure ? 'None' : 'Lax',
        ]);

        return $this->response->setJSON([
            'user' => $this->userService->getUserById($user->id_utilisateur),
            'accessToken' => $accessToken,
            'refreshToken' => $refreshToken,
            'mfaRequired' => false,
        ]);
    }
}
