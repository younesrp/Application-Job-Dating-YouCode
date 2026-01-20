<?php

namespace App\core;

class Security
{
    /**
     * Génère un token CSRF
     */
    
    public function generateCsrfToken(): string
    {
        if (!Session::getInstance()->has('_csrf_token')) {
            $token = bin2hex(random_bytes(32));
            Session::getInstance()->set('_csrf_token', $token);
        }

        return Session::getInstance()->get('_csrf_token');
    }

    /**
     * Vérifie le token CSRF
     */

    public function verifyCsrfToken(string $token): bool
    {
        $sessionToken = Session::getInstance()->get('_csrf_token');
        
        if (!$sessionToken || !$token) {
            return false;
        }

        return hash_equals($sessionToken, $token);
    }

    /**
     * Nettoie les données contre XSS
     */
    public function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }

        return htmlspecialchars(strip_tags($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valide et nettoie une email
     */

    public function sanitizeEmail(string $email): string
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Valide une URL
     */

    public function sanitizeUrl(string $url): string
    {
        return filter_var($url, FILTER_SANITIZE_URL);
    }

    /**
     * Hash un mot de passe de manière sécurisée
     */

    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    /**
     * Vérifie un mot de passe
     */

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Génère un token aléatoire sécurisé
     */

    public function generateToken(int $length = 32): string
    {
        return bin2hex(random_bytes($length));
    }

    /**
     * Protège contre les injections SQL (à utiliser avec PDO)
     */
    public function escapeString(string $string): string
    {
        return addslashes($string);
    }

    /**
     * Valide un email avec filter_var
     */
    public function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valide un mot de passe (au moins 8 caractères, 1 majuscule, 1 chiffre, 1 caractère spécial)
     */
    public function validatePassword(string $password): bool
    {
        if (strlen($password) < 8) {
            return false;
        }

        // Vérifier la présence d'au moins une majuscule, un chiffre et un caractère spécial
        $hasUppercase = preg_match('/[A-Z]/', $password);
        $hasLowercase = preg_match('/[a-z]/', $password);
        $hasNumber = preg_match('/[0-9]/', $password);
        $hasSpecialChar = preg_match('/[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]/', $password);

        return $hasUppercase && $hasLowercase && $hasNumber && $hasSpecialChar;
    }

    /**
     * Obtient le message d'erreur de validation du mot de passe
     */
    public function getPasswordValidationError(string $password): string
    {
        if (strlen($password) < 8) {
            return "Le mot de passe doit contenir au moins 8 caractères";
        }

        $errors = [];
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "au moins une majuscule";
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "au moins une minuscule";
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "au moins un chiffre";
        }
        if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:"\\|,.<>\/?]/', $password)) {
            $errors[] = "au moins un caractère spécial";
        }

        return "Le mot de passe doit contenir : " . implode(", ", $errors);
    }

    /**
     * Protège contre les injections SQL en utilisant des paramètres liés (recommandé)
     * Cette méthode suppose l'utilisation de PDO avec des prepared statements
     */
    public function preventSQLInjection(string $query, array $params = []): array
    {
        // Vérifier la présence de patterns dangereux dans la requête
        $dangerousPatterns = [
            "'; DROP TABLE",
            "1' OR '1'='1",
            "UNION SELECT",
            "/*",
            "--",
            "xp_",
            "sp_"
        ];

        $query = strtoupper($query);
        foreach ($dangerousPatterns as $pattern) {
            if (strpos($query, strtoupper($pattern)) !== false) {
                throw new \Exception("Motif SQL dangereux détecté dans la requête");
            }
        }

        // Nettoyer les paramètres
        $cleanedParams = array_map(function($param) {
            if (is_string($param)) {
                return $this->sanitize($param);
            }
            return $param;
        }, $params);

        return $cleanedParams;
    }

    /**
     * Valide une URL
     */
    public function validateUrl(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Valide une adresse IP
     */
    public function validateIP(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Valide un nombre entier
     */
    public function validateInteger($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Valide un nombre flottant
     */
    public function validateFloat($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    /**
     * Valide un booléen
     */
    public function validateBoolean($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null;
    }

    public static function againstHijacking(): void {
        header("X-Frame-Options: DENY");
        header("X-Content-Type-Options: nosniff");
        header("X-XSS-Protection: 1; mode=block");
        header("Referrer-Policy: strict-origin");
        header("Content-Security-Policy: frame-ancestors 'none'");
    }

}