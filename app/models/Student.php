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

    
}