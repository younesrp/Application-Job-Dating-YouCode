<?php
namespace App\Middlewares;

use App\core\MiddlewareInterface;

class AdminMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        if (!isset($_SESSION['admi'])) {
            header('Location: /login');
            exit;
        }
    }
}
