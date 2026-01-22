<?php

namespace App\models;

use App\core\Model;

class Company extends Model {
    
    protected $table = 'entreprises';
    protected $fillable = ['nom', 'secteur', 'ville', 'email', 'telephone', 'logo'];

    public function countAll() {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$this->table} WHERE is_archived = 0");
        return (int) $stmt->fetchColumn();
    }

    public function getAll() {
        return $this->pdo->query("SELECT * FROM {$this->table} ORDER BY created_at DESC")->fetchAll();
    }
    
    public function getOne($id) {
        return $this->find($id);
    }

    public function emailExists($email) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    public function archive($id) {
        return $this->update($id, ['is_archived' => 1]);
    }

    public function restore($id) {
        return $this->update($id, ['is_archived' => 0]);
    }
}