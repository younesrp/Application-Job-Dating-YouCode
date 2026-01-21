<?php

namespace App\models;

use App\core\Model;

class Announcement extends Model {
    
    // حددنا الجدول (مهم حيت BaseModel كيطلب $table)
    protected $table = 'annonces';

    public function countActive() {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE is_deleted = 0";
        // كنستعملو pdo مباشرة حيت هي protected ف BaseModel
        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    public function countArchived() {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE is_deleted = 1";
        return (int) $this->pdo->query($sql)->fetchColumn();
    }

    public function getRecent($limit = 3) {
        $sql = "SELECT a.*, e.nom as entreprise_nom 
                FROM {$this->table} a 
                JOIN entreprises e ON a.entreprise_id = e.id 
                WHERE a.is_deleted = 0 
                ORDER BY a.date_creation DESC 
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql);
        // BindValue ضروري مع LIMIT ف PDO
        $stmt->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}