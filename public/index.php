<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CompanyController;
use App\Controllers\HomeController;
use App\Controllers\OfferController;
use App\Controllers\ProfileController;
use App\Controllers\AdminController;
use App\Core\Router;
use App\Core\Container;
use App\Core\Database;

// Permet au serveur PHP integre de servir directement les fichiers statiques.
if (PHP_SAPI === 'cli-server') {
    $requestedPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $staticFile = __DIR__ . '/' . ltrim((string) $requestedPath, '/');

    if (is_file($staticFile)) {
        return false;
    }
}

// Autoload minimal: charge automatiquement les classes du namespace App\.
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    // Convertit App\Core\Router en chemin de fichier app/Core/Router.php.
    $relativeClass = substr($class, strlen($prefix));
    $file = __DIR__ . '/../app/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialise le conteneur de dépendances avec la DB.
$dbConfig = require __DIR__ . '/../config/database.php';
$database = new Database($dbConfig);
Container::set('db', $database->connect());

// Declaration des routes GET/POST de l'application.
$router = new Router();
$router->get('/', [HomeController::class, 'index']);
$router->get('/a-propos', [HomeController::class, 'about']);
$router->get('/offres', [OfferController::class, 'index']);
$router->post('/offres/publier', [OfferController::class, 'store']);
$router->post('/offres/fermer', [OfferController::class, 'close']);
$router->get('/offres/detail', [OfferController::class, 'show']);
$router->get('/offres/candidater', [OfferController::class, 'apply']);
$router->post('/offres/candidater', [OfferController::class, 'submitApplication']);
$router->get('/profils', [ProfileController::class, 'index']);
$router->get('/entreprises', [CompanyController::class, 'index']);
$router->get('/inscription', [AuthController::class, 'registerStudent']);
$router->post('/inscription', [AuthController::class, 'registerStudentSubmit']);
$router->get('/inscription-entreprise', [AuthController::class, 'registerCompany']);
$router->post('/inscription-entreprise', [AuthController::class, 'registerCompanySubmit']);
$router->get('/espace-entreprise', [AuthController::class, 'companySpace']);
$router->post('/connexion', [AuthController::class, 'loginSubmit']);
$router->post('/deconnexion', [AuthController::class, 'logoutSubmit']);
$router->get('/admin/login', [AdminController::class, 'loginPage']);
$router->post('/admin/login', [AdminController::class, 'loginSubmit']);
$router->post('/admin/creation', [AdminController::class, 'setupAdmin']);
$router->get('/profil', [AuthController::class, 'profileDashboard']);
$router->get('/profil/edition', [AuthController::class, 'editProfile']);
$router->post('/profil/edition', [AuthController::class, 'updateProfile']);
$router->get('/profil/messagerie', [AuthController::class, 'messages']);
$router->post('/profil/messagerie/envoyer', [AuthController::class, 'sendMessage']);
$router->get('/profil/candidatures', [AuthController::class, 'studentApplications']);
$router->get('/entreprise/candidatures', [AuthController::class, 'companyApplications']);
$router->post('/entreprise/candidature/statut', [AuthController::class, 'updateApplicationStatus']);

// Admin routes
$router->get('/admin', [AdminController::class, 'index']);
$router->get('/admin/utilisateurs', [AdminController::class, 'users']);
$router->post('/admin/utilisateurs/modifier', [AdminController::class, 'updateUserRole']);
$router->post('/admin/utilisateurs/supprimer', [AdminController::class, 'deleteUser']);

// Recupere la requete HTTP courante puis la confie au routeur.
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$router->dispatch($method, is_string($path) ? $path : '/');
