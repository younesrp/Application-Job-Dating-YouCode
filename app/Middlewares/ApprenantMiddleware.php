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
        
        // 1. Check if user is logged in
        if (!$session->has('user_id')) {
            $session->flash('errors', ['general' => ['Vous devez être connecté pour accéder à cette page.']]);
            header('Location: /login');
            exit;
        }
        
        // 2. Check session timeout (2 hours)
        if (isset($_SESSION['last_activity'])) {
            $inactive = time() - $_SESSION['last_activity'];
            if ($inactive > 7200) {
                $session->destroy();
                header('Location: /login?timeout=1');
                exit;
            }
        }
        $_SESSION['last_activity'] = time();
        
        // 3. Check if user has apprenant role
        if ($session->get('user_role') !== User::ROLE_APPRENANT) {
            $session->flash('errors', ['general' => ['Accès réservé aux apprenants.']]);
            header('Location: /admin/dashboard');
            exit;
        }
    }
}