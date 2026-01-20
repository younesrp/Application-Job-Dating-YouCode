<?php

namespace App\Core;

use PDO;
use App\Core\Database; 

class Auth
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Tente de connecter un utilisateur
     * * @param string $email
     * @param string $password
     * @param string $requiredRole Le rôle attendu pour cette connexion (ex: 'admin' ou 'apprenant')
     * @return bool Succès ou échec
     */
    public function login($email, $password, $requiredRole)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                
                if ($user['role'] !== $requiredRole) {
                    return false;
                }

                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['last_activity'] = time(); 

                return true;
            }
        }

        return false;
    }

    public function logout()
    {
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
 
        session_destroy();
    }

    /**
     * Vérifie si un utilisateur est connecté
     * Gère aussi l'expiration de session (2 heures)
     * * @return bool
     */
    public function isLoggedIn()
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }

        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 7200)) {
            $this->logout();
            return false;
        }
        $_SESSION['last_activity'] = time();
        return true;
    }

    /**
     * Récupère les informations de l'utilisateur connecté depuis la BDD
     * * @return array|false Données utilisateur ou false
     */
    public function getUser()
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $userId = $_SESSION['user_id'];
        $stmt = $this->db->prepare("SELECT id, email, role, created_at FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    /**
     * Vérifie si l'utilisateur connecté a le rôle requis
     * * @param string $requiredRole
     * @return bool
     */
    public function checkRole($requiredRole)
    {
        if (!$this->isLoggedIn()) {
            return false;
        }

        return $_SESSION['user_role'] === $requiredRole;
    }
    
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}