<?php

namespace App\models;

use App\core\Model;

class Company extends Model {
    
    protected $table = 'entreprises';

    public function countAll() {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    public function getAll() {
        return $this->pdo->query("SELECT * FROM {$this->table}")->fetchAll();
    }
}