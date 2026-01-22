<?php

namespace App\Middlewares;

use App\core\MiddlewareInterface;
use App\core\Session;
use App\models\User;

class AdminMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        $session = Session::getInstance();
        
        // 1. Vérifier si l'utilisateur est connecté
        if (!$session->has('user_id')) {
            $session->flash('errors', ['general' => ['Vous devez être connecté.']]);
            header('Location: /login');
            exit;
        }
        
        // 2. Vérifier si l'utilisateur a le rôle "admin"
        if ($session->get('user_role') !== User::ROLE_ADMIN) {
            // S'il n'est pas admin, on le redirige vers le dashboard apprenant
            $session->flash('errors', ['general' => ['Accès réservé aux administrateurs.']]);
            header('Location: /dashboard');
            exit;
        }
    }
}