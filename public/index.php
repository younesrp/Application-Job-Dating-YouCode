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
use App\controllers\back\DashboardController as AdminDashboardController;
use App\Middlewares\{AuthMiddleware, AdminMiddleware, ApprenantMiddleware};

$router = new Router();

require_once __DIR__ . '/../config/routes.php';

// Routes publiques
// public/index.php (ajouter ces routes)
 $router->get('/login', [AuthController::class, 'showLogin']);
 $router->post('/login', [AuthController::class, 'login']);
 $router->get('/register', [AuthController::class, 'showRegister']);
 $router->post('/register', [AuthController::class, 'register']);
 $router->get('/logout', [AuthController::class, 'logout']);

// Routes protégées
 $router->get('/dashboard', [FrontDashboardController::class, 'index'], [ApprenantMiddleware::class]);
 $router->post('/candidature', [CandidatureController::class, 'apply'], [ApprenantMiddleware::class]);
 $router->get('/candidatures', [FrontDashboardController::class, 'candidatures'], [ApprenantMiddleware::class]);
 $router->get('/profil', [FrontDashboardController::class, 'profil'], [ApprenantMiddleware::class]);
 $router->post('/profil/update', [FrontDashboardController::class, 'updateProfil'], [ApprenantMiddleware::class]);
 $router->get('/offres', [JobController::class, 'index'], [ApprenantMiddleware::class]);
 $router->get('/offres/{id}', [FrontDashboardController::class, 'detailOffre'], [ApprenantMiddleware::class]);
 $router->get('/admin/dashboard', [AdminDashboardController::class, 'index'], [AdminMiddleware::class]);
 $router->get('/admin/candidatures', [AdminDashboardController::class, 'candidatures'], [AdminMiddleware::class]);
 $router->post('/admin/candidatures/{candidatureId}/status', [AdminDashboardController::class, 'updateCandidatureStatus'], [AdminMiddleware::class]);
 $router->get('/admin/cv/{filename}', [AdminDashboardController::class, 'downloadCv'], [AdminMiddleware::class]);

// Route publique pour accéder aux fichiers (sans middleware)
 $router->get('/uploads/cvs/{filename}', [AdminDashboardController::class, 'serveCv']);

// Déconnexion
$router->get('/logout', [AuthController::class, 'logout']);

// Route racine
$router->get('/', function() {
    header('Location: /login');
    exit();
});

$router->dispatch();