<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use App\core\Router;
use App\controllers\front\{AuthController};
use App\controllers\back\{DashboardController,CompanyController,AnnoncesController,StudentController};
use App\Middlewares\{AuthMiddleware, AdminMiddleware, ApprenantMiddleware, GuestMiddleware};

$router = new Router();

require_once __DIR__ . '/../config/routes.php';

// Routes publiques
// public/index.php (ajouter ces routes)
 $router->get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
 $router->post('/login', [AuthController::class, 'login']);
 $router->get('/register', [AuthController::class, 'showRegister'], [GuestMiddleware::class]);
 $router->post('/register', [AuthController::class, 'register']);


$router->get('/admin/entreprises', [CompanyController::class, 'index'], [AdminMiddleware::class]);
$router->post('/admin/entreprises', [CompanyController::class, 'store'], [AdminMiddleware::class]);

$router->get('/admin/annonces', [AnnoncesController::class, 'index'], [AdminMiddleware::class]);
$router->get('/admin/companies', [CompanyController::class, 'index'], [AdminMiddleware::class]);
$router->post('/companies', [CompanyController::class, 'store'], [AdminMiddleware::class]);

// Routes protégées
$router->get('/dashboard', [\App\controllers\front\DashboardController::class, 'index'], [ApprenantMiddleware::class]);
$router->get('/admin/dashboard', [DashboardController::class, 'index'], [AdminMiddleware::class]);

// Routes protégées
$router->get('/dashboard', [\App\controllers\front\DashboardController::class, 'index'], [ApprenantMiddleware::class]);
$router->get('/admin/dashboard', [DashboardController::class, 'index'], [AdminMiddleware::class]);

$router->get('/logout', [AuthController::class, 'logout']);

// Route racine
$router->get('/', function() {
    session_start();
    if (isset($_SESSION['user_id'])) {
        $role = $_SESSION['user_role'] ?? '';
        if ($role === 'admin') {
            header('Location: /admin/dashboard');
        } else {
            header('Location: /dashboard');
        }
    } else {
        header('Location: /login');
    }
    exit();
});


// dispatch request
$router->dispatch();
