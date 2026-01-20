<?php

namespace App\Models;

use App\Core\Model;

class Company extends Model {

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM entreprises";
        return $this->db->query($sql)->fetch()['total'];
    }
}