<?php
ini_set('memory_limit', '256M');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use App\core\Router;
use App\controllers\front\AuthController;
use App\controllers\front\CandidatureController;
use App\controllers\front\DashboardController as FrontDashboardController;
use App\controllers\front\JobController;
use App\Middlewares\{ApprenantMiddleware};

$router = new Router();

require_once __DIR__ . '/../config/routes.php';

// Routes publiques
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// Routes apprenants
$router->get('/dashboard', [FrontDashboardController::class, 'index'], [ApprenantMiddleware::class]);
$router->post('/candidature', [CandidatureController::class, 'apply'], [ApprenantMiddleware::class]);
$router->get('/candidatures', [FrontDashboardController::class, 'candidatures'], [ApprenantMiddleware::class]);
$router->get('/profil', [FrontDashboardController::class, 'profil'], [ApprenantMiddleware::class]);
$router->post('/profil/update', [FrontDashboardController::class, 'updateProfil'], [ApprenantMiddleware::class]);
$router->get('/offres', [JobController::class, 'index'], [ApprenantMiddleware::class]);
$router->post('/api/search', [JobController::class, 'search'], [ApprenantMiddleware::class]);
$router->get('/offres/{id}', [FrontDashboardController::class, 'detailOffre'], [ApprenantMiddleware::class]);

// Route racine
$router->get('/', function() {
    header('Location: /login');
    exit();
});

$router->dispatch();