<?php
namespace App\Middlewares;

use App\core\MiddlewareInterface;

class Middleware implements MiddlewareInterface
{
    public function handle(): void
    {
        if (isset($_SESSION['user'])) { 
            header('Location: /dashboard'); 
            exit;
        }
    }
}
