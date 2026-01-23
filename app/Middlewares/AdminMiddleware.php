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
        
        // 1. Check if user is logged in
        if (!$session->has('user_id')) {
            $session->flash('errors', ['general' => ['Vous devez être connecté.']]);
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
        
        // 3. Check if user has admin role
        if ($session->get('user_role') !== User::ROLE_ADMIN) {
            $session->flash('errors', ['general' => ['Accès réservé aux administrateurs.']]);
            header('Location: /dashboard');
            exit;
        }
        
        // 4. Regenerate session ID periodically (every 30 min)
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
        
        // 5. Security headers for admin panel
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}