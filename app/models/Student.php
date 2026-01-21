<?php

namespace App\models;

use App\core\Model;

class Student extends Model {
    
    protected $table = 'apprenants';

    public function countAll() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        return (int) $this->pdo->query($sql)->fetchColumn();
    }
}