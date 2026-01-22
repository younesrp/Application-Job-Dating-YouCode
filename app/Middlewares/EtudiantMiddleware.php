<?php
namespace App\Middlewares;

use App\core\MiddlewareInterface;
use App\core\Session;
use App\models\User;

/**
 * Middleware pour les étudiants/apprenants
 * Vérifie que l'utilisateur est connecté et a le rôle 'apprenant'
 */
class EtudiantMiddleware implements MiddlewareInterface
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
        
        // Vérifier si l'utilisateur a le rôle 'apprenant'
        if ($session->get('user_role') !== User::ROLE_APPRENANT) {
            $session->flash('errors', ['general' => ['Accès réservé aux apprenants.']]);
            header('Location: /admin/dashboard');
            exit;
        }
    }
}
