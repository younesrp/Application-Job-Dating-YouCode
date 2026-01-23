<?php

namespace App\models;

use App\core\Model;

class Company extends Model {
    
    protected $table = 'entreprises';

    public function getAll() {
        return $this->pdo->query("SELECT * FROM {$this->table} ORDER BY id DESC")->fetchAll();
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    public function countAll() {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    public function create($data) {
        $sql = "INSERT INTO {$this->table} (nom, secteur, ville, email, telephone, logo) 
                VALUES (:nom, :secteur, :ville, :email, :telephone, :logo)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} 
                SET nom = :nom, secteur = :secteur, ville = :ville, 
                    email = :email, telephone = :telephone 
                WHERE id = :id";
                
        $data['id'] = $id; 
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}