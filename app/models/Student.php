<?php

namespace App\models;

use App\core\Model;

class Student extends Model {
    
    protected $table = 'apprenants';

    public function countAll() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        return (int) $this->pdo->query($sql)->fetchColumn();
    }
    public function getAllStudents() {
        $sql = "SELECT 
                    u.id, 
                    u.email, 
                    a.nom, 
                    a.prenom, 
                    a.promotion, 
                    a.specialisation 
                FROM users u
                JOIN apprenants a ON u.id = a.user_id
                WHERE u.role = 'apprenant'
                ORDER BY a.nom ASC";
                
        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * Trouve un apprenant par son user_id
     */
    public function findByUserId($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch();
    }

    /**
     * Met à jour les informations d'un apprenant
     */
    public function updateStudent($userId, $data) {
        $updates = [];
        $params = [':user_id' => $userId];

        // Colonnes qui peuvent être mises à jour dans la table apprenants
        $validColumns = ['nom', 'prenom', 'promotion', 'specialisation'];

        foreach ($validColumns as $column) {
            if (isset($data[$column]) && $data[$column] !== null) {
                $updates[] = "{$column} = :{$column}";
                $params[":{$column}"] = $data[$column];
            }
        }

        if (empty($updates)) {
            return true;
        }

        $sql = "UPDATE {$this->table} SET " . implode(", ", $updates) . " WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
    
}