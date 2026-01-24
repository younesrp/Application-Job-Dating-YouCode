<?php



use App\controllers\back\DashboardController;
use App\controllers\back\AnnoncesController;
use App\controllers\back\StudentController;
use App\controllers\back\CandidatureController;
use App\controllers\home\testControl;

use App\controllers\back\CompanyController;
$router->get('/admin/companies', [CompanyController::class, 'index']);

$router->get('/admin/companies/create', [CompanyController::class, 'create']);
$router->post('/admin/companies/save', [CompanyController::class, 'save']);

$router->get('/admin/companies/edit/{id}', [CompanyController::class, 'edit']);
$router->post('/admin/companies/update/{id}', [CompanyController::class, 'update']);

$router->get('/admin/companies/delete/{id}', [CompanyController::class, 'delete']);


$router->post('/admin/candidatures/update/{id}', [CandidatureController::class, 'updateStatus']);


$router->get('/admin/dashboard', [DashboardController::class, 'index']);

$router->get('/', function() {
    echo "<h1>Bienvenue sur la page d'accueil !</h1>";
});
$router->get('/admin/annonces/create', [AnnoncesController::class, 'create']);

$router->post('/admin/annonces/save', [AnnoncesController::class, 'save']);


$router->get('/admin/annonces', [AnnoncesController::class, 'index']);

$router->get('/admin/annonces/delete/{id}', [AnnoncesController::class, 'delete']);

$router->get('/admin/annonces/edit/{id}', [AnnoncesController::class, 'edit']);

$router->post('/admin/annonces/update/{id}', [AnnoncesController::class, 'update']);
// 1. Import Class

// 2. Add Route
$router->get('/admin/students', [StudentController::class, 'index']);


// ...
$router->get('/admin/candidatures', [CandidatureController::class, 'index']);