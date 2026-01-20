<?php

namespace App\app\models;
use App\app\core\BaseModel;

class Student extends BaseModel {


    // Optional: Override table name if it doesn't follow the plural convention
    // protected $table = 'app_users';
    
    
   public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM apprenants";
        return $this->db->query($sql)->fetch()['total'];
    }
    
}