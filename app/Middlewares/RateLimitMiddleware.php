<?php

namespace App\Middlewares;

use App\core\MiddlewareInterface;
use App\core\Session;

class RateLimitMiddleware implements MiddlewareInterface
{
    private const MAX_REQUESTS = 60; // Max requests
    private const TIME_WINDOW = 60; // Per 60 seconds
    
    public function handle(): void
    {
        $session = Session::getInstance();
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = 'rate_limit_' . md5($ip);
        
        $requests = $session->get($key, []);
        $now = time();
        
        // Remove old requests outside time window
        $requests = array_filter($requests, function($timestamp) use ($now) {
            return ($now - $timestamp) < self::TIME_WINDOW;
        });
        
        // Check if limit exceeded
        if (count($requests) >= self::MAX_REQUESTS) {
            http_response_code(429);
            header('Retry-After: ' . self::TIME_WINDOW);
            die('Trop de requêtes. Veuillez réessayer dans ' . self::TIME_WINDOW . ' secondes.');
        }
        
        // Add current request
        $requests[] = $now;
        $session->set($key, $requests);
    }
}
