<?php
namespace App\app\core;

use Illuminate\Database\Capsule\Manager as Capsule; 
// (constants) paramettres
require_once __DIR__ . '/../../config/config.php'; 
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
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    // 2️⃣ إعداد Eloquent (للكود الجديد)
        $this->capsule = new Capsule;

        $this->capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => DB_HOST,
            'database'  => DB_NAME,
            'username'  => DB_USER,
            'password'  => DB_PASS,
            'charset'   => DB_CHARSET,
            'collation' => DB_CHARSET . '_unicode_ci',
            'prefix'    => '',
        ]);

        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
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
     * Eloquent
     */
    public function getCapsule(): Capsule
    {
        return $this->capsule;
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