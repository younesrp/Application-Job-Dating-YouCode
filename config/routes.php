<?php

use App\controllers\back\DashboardController;
use App\controllers\back\AnnoncesController;

// Dashboard
$router->get('admin/dashboard', [DashboardController::class, 'index']);

// Home page
$router->get('/', function() {
    echo "<h1>Bienvenue sur la page d'accueil !</h1>";
});

// Annonces routes
$router->get('/admin/annonces', [AnnoncesController::class, 'index']);
$router->get('/admin/annonces/create', [AnnoncesController::class, 'create']);
$router->post('/admin/annonces/save', [AnnoncesController::class, 'save']);
$router->get('/admin/annonces/delete/{id}', [AnnoncesController::class, 'delete']);
$router->get('/admin/annonces/edit/{id}', [AnnoncesController::class, 'edit']);
$router->post('/admin/annonces/update/{id}', [AnnoncesController::class, 'update']);
