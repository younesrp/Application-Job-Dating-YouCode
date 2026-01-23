<?php

namespace App\Middlewares;

use App\core\MiddlewareInterface;
use App\core\Session;

class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        $session = Session::getInstance();
        
        // Only check CSRF for POST, PUT, DELETE requests
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $token = $_POST['_token'] ?? $_POST['csrf_token'] ?? '';
            $sessionToken = $session->get('csrf_token');
            
            if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
                http_response_code(403);
                $session->flash('errors', ['general' => ['Token CSRF invalide. Veuillez réessayer.']]);
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
                exit;
            }
        }
    }
}
