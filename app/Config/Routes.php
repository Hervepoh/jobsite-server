<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Api\AuthController;
use App\Controllers\Api\CvController;
use App\Controllers\Api\OfferController;
use App\Controllers\Api\SessionController;


/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');


$routes->group('/api/v1/', ['filter' => 'secureCORS'], static function (RouteCollection $routes): void {
    // Requêtes préflight (OPTIONS)
    $routes->options('auth/register', [AuthController::class, 'register']);
    $routes->options('auth/login', [AuthController::class, 'login']);
    $routes->options('auth/refresh',  [AuthController::class, 'refresh']);

    // Routes publiques
    $routes->post('auth/register', [AuthController::class, 'register']);
    $routes->post('auth/login',  [AuthController::class, 'login']);
    $routes->post('auth/verify/email',  [AuthController::class, 'verifyEmail']);
    $routes->post('auth/password/forgot',  [AuthController::class, 'passwordForgot']);
    $routes->post('auth/password/reset',  [AuthController::class, 'passwordReset']);
    $routes->get('auth/refresh',  [AuthController::class, 'refresh']);

    $routes->get('offres',  [OfferController::class, 'index']);
    $routes->get('offres/(:num)',  [OfferController::class, 'show']);
    $routes->get('offres/active',  [OfferController::class, 'active']);
    $routes->get('offres/last',  [OfferController::class, 'last']);


    // Routes protégées par JWT
    $routes->group('', ['filter' => 'authJWT'], static function (RouteCollection $routes): void {
        $routes->post('auth/logout', [AuthController::class, 'logout']);
        // Gestion des sessions de connection
        $routes->get('session', [SessionController::class, 'getSession']);
        $routes->get('session/all', [SessionController::class, 'getAllSessions']);
        $routes->delete('session/(:segment)',  [SessionController::class, 'deleteSession/$1']);

        // Utilisateur connecté peut postuler à une offre
        $routes->post('offres/(:num)/apply',  [OfferController::class, 'apply']);
        // liste des offres de l'utilisateur connecté
        $routes->get('offres/me',  [OfferController::class, 'my']);

        $routes->post('cv/langues',  [CvController::class, 'save_langue']);
        $routes->put('cv/langues/(:num)',  [CvController::class, 'update_langue/$1']);
        $routes->delete('cv/langues/(:num)',  [CvController::class, 'delete_langue/$1']);
        $routes->get('cv/langues',  [CvController::class, 'getAll_langues/$1']);

        $routes->post('cv/cursus',  [CvController::class, 'save_cursus']);
        $routes->put('cv/cursus/(:num)',  [CvController::class, 'update_cursus/$1']);
        $routes->delete('cv/cursus/(:num)',  [CvController::class, 'delete_cursus/$1']);
        $routes->get('cv/cursus',  [CvController::class, 'getAll_cursus/$1']);

        $routes->post('cv/formations',  [CvController::class, 'save_formation']);
        $routes->put('cv/formations/(:num)',  [CvController::class, 'update_formation/$1']);
        $routes->delete('cv/formations/(:num)',  [CvController::class, 'delete_formation/$1']);
        $routes->get('cv/formations',  [CvController::class, 'getAll_Formations/$1']);

        $routes->post('cv/associations',  [CvController::class, 'save_association']);
        $routes->put('cv/associations/(:num)',  [CvController::class, 'update_association/$1']);
        $routes->delete('cv/associations/(:num)',  [CvController::class, 'delete_association/$1']);
        $routes->get('cv/associations',  [CvController::class, 'getAll_associations/$1']);

        $routes->post('cv/attestations',  [CvController::class, 'save_attestation']);
        $routes->put('cv/attestations/(:num)',  [CvController::class, 'update_attestation/$1']);
        $routes->delete('cv/attestations/(:num)',  [CvController::class, 'delete_attestation/$1']);
        $routes->get('cv/attestations',  [CvController::class, 'getAll_attestations/$1']);

        $routes->post('cv/competences',  [CvController::class, 'save_competence']);
        $routes->put('cv/competences/(:num)',  [CvController::class, 'update_competence/$1']);
        $routes->delete('cv/competences/(:num)',  [CvController::class, 'delete_competence/$1']);
        $routes->get('cv/competences',  [CvController::class, 'getAll_competences/$1']);
    });
});
