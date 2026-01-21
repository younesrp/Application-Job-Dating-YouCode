<?php
// composer autoload
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// echo "<pre>";
// print_r($_ENV['DB_NAME']); //job_dating_youcode
// echo "</pre>";
// die();

use App\core\Router;
use App\controllers\front\{AuthController};
use App\controllers\back\{CompanyController, AnnouncementController, DashboardController, StudentController};

use App\Middlewares\{Middleware,EtudiantMiddleware};
$router = new Router();

//require_once __DIR__ . '/../config/config.php'; 


$router->get('/dashboard', [AuthController::class, 'dashboard'], [EtudiantMiddleware::class]);
$router->post('/dashboard', [AuthController::class, 'dashboard'], [EtudiantMiddleware::class]);
$router->get('/login', [AuthController::class, 'showLogin'], [Middleware::class]);
$router->post('/login', [AuthController::class, 'login'], [Middleware::class]);
$router->get('/register', [AuthController::class, 'showRegister'], [Middleware::class]);
$router->post('/register', [AuthController::class, 'register'], [Middleware::class]);

$router->get('/Company', [CompanyController::class, 'company']);
$router->post('/Company', [CompanyController::class, 'company']);

$router->get('/logout', function() {session_unset();session_destroy();
    header('Location: /login');
    exit();
});


// dispatch request
$router->dispatch();    