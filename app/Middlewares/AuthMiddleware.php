<?php

namespace App\Middlewares;

use App\core\MiddlewareInterface;
use App\core\Session;

/**
 * Middleware d'authentification de base
 * Vérifie uniquement si l'utilisateur est connecté (peu importe son rôle)
 * Gère également l'expiration de session (timeout de 2 heures)
 */
class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        $session = Session::getInstance();
        
        // Vérifier si la session a expiré (timeout de 2 heures)
        if ($session->has('last_activity')) {
            $timeout = 7200; // 2 heures en secondes
            
            if (time() - $session->get('last_activity') > $timeout) {
                $session->destroy();
                $session->flash('errors', ['general' => ['Votre session a expiré. Veuillez vous reconnecter.']]);
                header('Location: /login');
                exit;
            }
        }
        
        // Mettre à jour le timestamp de dernière activité
        $session->set('last_activity', time());
        
        // Vérifier si l'utilisateur est connecté
        if (!$session->has('user_id')) {
            $session->flash('errors', ['general' => ['Vous devez être connecté pour accéder à cette page.']]);
            header('Location: /login');
            exit;
        }
    }
}