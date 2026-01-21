<?php

namespace App\models;

use App\core\Model;
use PDO;

class Company extends Model
{
    protected $table = 'companies';
    protected $db;
    /**
     * Trouver une entreprise par email
     */
    public function findByEmail($email)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
            $stmt = $this->db->query($sql, [$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in findByEmail: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer une nouvelle entreprise
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO {$this->table} (nom, secteur, ville, email, telephone, logo, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $this->db->query($sql, [
                $data['nom'],
                $data['secteur'],
                $data['ville'],
                $data['email'],
                $data['telephone'],
                $data['logo'] ?? null
            ]);
            
            return true;
        } catch (\Exception $e) {
            error_log("Error in create: " . $e->getMessage());
            throw new \Exception("Erreur lors de la création de l'entreprise");
        }
    }

    /**
     * Compter toutes les entreprises
     */
    public function countAll()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            $result = $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (\Exception $e) {
            error_log("Error in countAll: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Récupérer toutes les entreprises
     */
    public function getAll()
    {
        try {
            $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getAll: " . $e->getMessage());
            return [];
        }
    }
}