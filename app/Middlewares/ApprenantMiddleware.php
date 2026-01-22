<?php

namespace App\Middlewares;

use App\core\MiddlewareInterface;
use App\core\Session;
use App\models\User;

class ApprenantMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        $session = Session::getInstance();
        
        // 1. Vérifier si l'utilisateur est connecté
        if (!$session->has('user_id')) {
            $session->flash('errors', ['general' => ['Vous devez être connecté pour accéder à cette page.']]);
            header('Location: /login');
            exit;
        }
        
        // 2. Vérifier si l'utilisateur a le rôle "apprenant"
        if ($session->get('user_role') !== User::ROLE_APPRENANT) {
            // S'il n'est pas apprenant, on le redirige vers le dashboard admin
            $session->flash('errors', ['general' => ['Accès réservé aux apprenants.']]);
            header('Location: /admin/dashboard');
            exit;
        }
        
        // Si tout est bon, on continue vers la page demandée
    }
}