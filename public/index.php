<?php
// composer autoload

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// var_dump($_ENV['DB_NAME']);

use App\core\Router;
use App\controllers\AuthController;
use App\Middlewares\{Middleware, EtudiantMiddleware};

$router = new Router();

require_once __DIR__ . '/../config/routes.php'; 


$router->get('/dashboard', [AuthController::class, 'dashboard'], [EtudiantMiddleware::class]);
$router->post('/dashboard', [AuthController::class, 'dashboard'], [EtudiantMiddleware::class]);
$router->get('/login', [AuthController::class, 'showLogin'], [Middleware::class]);
$router->post('/login', [AuthController::class, 'login'], [Middleware::class]);
$router->get('/register', [AuthController::class, 'showRegister'], [Middleware::class]);
$router->post('/register', [AuthController::class, 'register'], [Middleware::class]);

// dispatch request
$router->dispatch();    