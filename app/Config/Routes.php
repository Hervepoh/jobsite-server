<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Api\AuthController;
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
        $routes->delete('session/(:segment)',  [SessionController::class, 'deleteSession/$1']);

        // Utilisateur connecté peut postuler à une offre
        $routes->post('offres/(:num)/apply',  [OfferController::class, 'apply']);
        // liste des offres de l'utilisateur connecté
        $routes->get('offres/me',  [OfferController::class, 'my']);

    });
});
