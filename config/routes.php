<?php

use App\controllers\back\{DashboardController, AnnoncesController, StudentController, CandidatureController, CompanyController};
use App\Middlewares\AdminMiddleware;

// ===== ADMIN ROUTES - Protected by AdminMiddleware =====

// Companies
$router->get('/admin/companies', [CompanyController::class, 'index'], [AdminMiddleware::class]);
$router->get('/admin/companies/create', [CompanyController::class, 'create'], [AdminMiddleware::class]);
$router->post('/admin/companies/save', [CompanyController::class, 'save'], [AdminMiddleware::class]);
$router->get('/admin/companies/edit/{id}', [CompanyController::class, 'edit'], [AdminMiddleware::class]);
$router->post('/admin/companies/update/{id}', [CompanyController::class, 'update'], [AdminMiddleware::class]);
$router->get('/admin/companies/delete/{id}', [CompanyController::class, 'delete'], [AdminMiddleware::class]);

// Annonces
$router->get('/admin/annonces', [AnnoncesController::class, 'index'], [AdminMiddleware::class]);
$router->get('/admin/annonces/create', [AnnoncesController::class, 'create'], [AdminMiddleware::class]);
$router->post('/admin/annonces/save', [AnnoncesController::class, 'save'], [AdminMiddleware::class]);
$router->get('/admin/annonces/edit/{id}', [AnnoncesController::class, 'edit'], [AdminMiddleware::class]);
$router->post('/admin/annonces/update/{id}', [AnnoncesController::class, 'update'], [AdminMiddleware::class]);
$router->get('/admin/annonces/delete/{id}', [AnnoncesController::class, 'delete'], [AdminMiddleware::class]);

// Students
$router->get('/admin/students', [StudentController::class, 'index'], [AdminMiddleware::class]);

// Candidatures
$router->get('/admin/candidatures', [CandidatureController::class, 'index'], [AdminMiddleware::class]);
$router->post('/admin/candidatures/update/{id}', [CandidatureController::class, 'updateStatus'], [AdminMiddleware::class]);
$router->post('/admin/candidatures/{candidatureId}/status', [DashboardController::class, 'updateCandidatureStatus'], [AdminMiddleware::class]);
$router->get('/admin/cv/{filename}', [DashboardController::class, 'downloadCv'], [AdminMiddleware::class]);

// Dashboard
$router->get('/admin/dashboard', [DashboardController::class, 'index'], [AdminMiddleware::class]);

// Public file access (no middleware)
$router->get('/uploads/cvs/{filename}', [DashboardController::class, 'serveCv']);