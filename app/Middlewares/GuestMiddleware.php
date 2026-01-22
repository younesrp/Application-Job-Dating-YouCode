<?php

namespace App\Middlewares;

use App\core\MiddlewareInterface;
use App\core\Session;

/**
 * Middleware pour les visiteurs (guests)
 * Redirige les utilisateurs connectés vers leur dashboard respectif
 * Permet uniquement l'accès aux pages publiques (login/register) pour les non-connectés
 */
class GuestMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        $session = Session::getInstance();
        
        // Si l'utilisateur est déjà connecté, redirection vers son dashboard
        if ($session->has('user_id')) {
            $role = $session->get('user_role');
            
            // Redirection selon le rôle
            if ($role === 'admin') {
                header('Location: /admin/dashboard');
            } else {
                header('Location: /dashboard');
            }
            exit;
        }
    }
}
