<?php
namespace App\Middlewares;

use App\core\MiddlewareInterface;

class EtudiantMiddleware implements MiddlewareInterface
{
    public function handle() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }
}
