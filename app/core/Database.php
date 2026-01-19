<?php
namespace App\app\core;

 
// (constants) .env
require_once __DIR__ . '/../../.env';
// global class f php
use PDO;
use PDOException;
//include_once '../../config/database.php';
class Database{
    private static $instance = null;
    private $connection; // PDO
    private $capsule;    // Eloquent
    
    /**
     * Constructeur privé pour empêcher l'instanciation directe
     */
    private function __construct() {
       try {
            $dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME']. ";charset=" . $_ENV['DB_CHARSET'];
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn,$_ENV['DATABASE_USER'], $_ENV['DATABASE_PASSWORD'], $options);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }

    }
    
    /**
     * Empêche le clonage de l'instance
     */
    private function __clone() {}
    
    /**
     * Récupère l'instance unique de Database
     * @ return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Récupère la connexion PDO
     * @return PDO
     */
    public function getConnection(): PDO {
        return $this->connection;
    }
    
    /**
     * Prépare et exécute une requête
     * @ param string $sql
     * @ param array $params
     * @ return PDOStatement
     */
    function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}