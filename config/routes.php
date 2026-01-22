<?php

use App\controllers\back\{DashboardController, AnnoncesController, CompanyController};
use App\Middlewares\AdminMiddleware;

// Dashboard
$router->get('admin/dashboard', [DashboardController::class, 'index'], [AdminMiddleware::class]);

// Annonces routes
$router->get('/admin/annonces', [AnnoncesController::class, 'index'], [AdminMiddleware::class]);
$router->get('/admin/annonces/create', [AnnoncesController::class, 'create'], [AdminMiddleware::class]);
$router->post('/admin/annonces/save', [AnnoncesController::class, 'save'], [AdminMiddleware::class]);
$router->get('/admin/annonces/delete/{id}', [AnnoncesController::class, 'delete'], [AdminMiddleware::class]);
$router->get('/admin/annonces/edit/{id}', [AnnoncesController::class, 'edit'], [AdminMiddleware::class]);
$router->post('/admin/annonces/update/{id}', [AnnoncesController::class, 'update'], [AdminMiddleware::class]);

// Companies routes
$router->get('/admin/companies', [CompanyController::class, 'index'], [AdminMiddleware::class]);
$router->post('/admin/companies/store', [CompanyController::class, 'store'], [AdminMiddleware::class]);
$router->post('/admin/companies/update/{id}', [CompanyController::class, 'update'], [AdminMiddleware::class]);
$router->get('/admin/companies/archive/{id}', [CompanyController::class, 'archive'], [AdminMiddleware::class]);
$router->get('/admin/companies/restore/{id}', [CompanyController::class, 'restore'], [AdminMiddleware::class]);
