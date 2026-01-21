<?php

namespace App\models;

use App\core\Model;

class Company extends Model {
    
    protected $table = 'entreprises';

    public function countAll() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        return (int) $this->pdo->query($sql)->fetchColumn();
    }
}