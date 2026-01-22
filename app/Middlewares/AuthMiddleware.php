<?php

namespace App\Middlewares;

use App\core\MiddlewareInterface;
use App\core\Session;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        $session = Session::getInstance();
        
        // Vérifie simplement si un utilisateur est connecté (peu importe son rôle)
        if (!$session->has('user_id')) {
            $session->flash('errors', ['general' => ['Vous devez être connecté pour accéder à cette page.']]);
            header('Location: /login');
            exit;
        }
    }
}