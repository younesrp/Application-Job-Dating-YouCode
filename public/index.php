<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use App\core\Router;
use App\controllers\front\{AuthController};
use App\controllers\back\{DashboardController,CompanyController,AnnoncesController,StudentController};
use App\Middlewares\{AuthMiddleware, AdminMiddleware, ApprenantMiddleware};

$router = new Router();

require_once __DIR__ . '/../config/routes.php';

// Routes publiques
// public/index.php (ajouter ces routes)
 $router->get('/login', [AuthController::class, 'showLogin']);
 $router->post('/login', [AuthController::class, 'login']);
 $router->get('/register', [AuthController::class, 'showRegister']);
 $router->post('/register', [AuthController::class, 'register']);


$router->get('/admin/entreprises', [CompanyController::class, 'showCompany']);
$router->post('/admin/entreprises', [CompanyController::class, 'company']);

$router->get('/admin/annonces', [AnnoncesController::class, 'index'], [AdminMiddleware::class]);
$router->get('/admin/companies', [CompanyController::class, 'showCompany'], [AdminMiddleware::class]);
$router->post('/companies', [CompanyController::class, 'company'], [AdminMiddleware::class]);

$router->get('/logout', function() {session_unset();session_destroy();});

// Routes protégées
 $router->get('/dashboard', [DashboardController::class, 'index'], [ApprenantMiddleware::class]);
 $router->get('/admin/dashboard', [DashboardController::class, 'index'], [AdminMiddleware::class]);

// Déconnexion
$router->get('/logout', [AuthController::class, 'logout']);

// Route racine
$router->get('/', function() {
    header('Location: /login');
    exit();
});


// dispatch request
$router->dispatch();
