<?php

namespace App\models;

use App\core\Model;
use PDO;

class Announcement extends Model {
    
    protected $table = 'annonces';

    public function countActive() {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE is_deleted = 0";
        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    public function countArchived() {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE is_deleted = 1";
        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    public function getRecent($limit = 3) {
        $sql = "SELECT a.*, e.nom as entreprise_nom 
                FROM {$this->table} a 
                LEFT JOIN entreprises e ON a.entreprise_id = e.id 
                WHERE a.is_deleted = 0 
                ORDER BY a.date_creation DESC 
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getAll() {
        $sql = "SELECT a.*, e.nom as entreprise_nom 
                FROM {$this->table} a 
                LEFT JOIN entreprises e ON a.entreprise_id = e.id 
                ORDER BY a.date_creation DESC";
                
        return $this->pdo->query($sql)->fetchAll();
    }

    public function search($query) {
        $sql = "SELECT a.*, e.nom as entreprise_nom 
                FROM {$this->table} a 
                LEFT JOIN entreprises e ON a.entreprise_id = e.id 
                WHERE (a.titre LIKE :query OR a.description LIKE :query)
                AND a.is_deleted = 0
                ORDER BY a.date_creation DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':query' => '%' . $query . '%']);
        return $stmt->fetchAll();
    }

    public function create($data)
    {
    
        $sql = "INSERT INTO {$this->table} (titre, description, date_creation, entreprise_id) 
                VALUES (:titre, :description, NOW(), :entreprise_id)";
        
        $stmt = $this->pdo->prepare($sql);
        
        return $stmt->execute([
            ':titre' => $data['titre'],
            ':description' => $data['description'],
            ':entreprise_id' => 1 
        ]);
    }

public function delete($id) {
    $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([':id' => $id]);


}

public function find($id) {
    $sql = "SELECT * FROM {$this->table} WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
}

public function update($id, $data) {
    $sql = "UPDATE {$this->table} 
            SET titre = :titre, 
                description = :description, 
                entreprise_id = :entreprise_id,
                is_deleted = :is_deleted  
            WHERE id = :id";
            
    $stmt = $this->pdo->prepare($sql);
    
    return $stmt->execute([
        ':titre' => $data['titre'],
        ':description' => $data['description'],
        ':entreprise_id' => $data['entreprise_id'],
        ':is_deleted' => $data['is_deleted'], 
        ':id' => $id
    ]);
}
}