<?php

namespace App\Middlewares;

use App\core\MiddlewareInterface;
use App\core\Session;
use App\models\User;

/**
 * Middleware pour les administrateurs
 * Vérifie que l'utilisateur est connecté et a le rôle 'admin'
 */
class AdminMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        $session = Session::getInstance();
        
        // Vérifier si l'utilisateur est connecté
        if (!$session->has('user_id')) {
            $session->flash('errors', ['general' => ['Vous devez être connecté.']]);
            header('Location: /login');
            exit;
        }
        
        // Vérifier si l'utilisateur a le rôle 'admin'
        if ($session->get('user_role') !== User::ROLE_ADMIN) {
            $session->flash('errors', ['general' => ['Accès réservé aux administrateurs.']]);
            header('Location: /dashboard');
            exit;
        }
    }
}