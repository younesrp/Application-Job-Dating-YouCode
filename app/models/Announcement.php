<?php

namespace App\Models;

use App\Core\Model;

class Announcement extends Model {

    // Hsab chhal mn offre active (is_deleted = 0)
    public function countActive() {
        $sql = "SELECT COUNT(*) as total FROM annonces WHERE is_deleted = 0";
        return $this->db->query($sql)->fetch()['total'];
    }

    // Hsab chhal mn offre archivée (is_deleted = 1)
    public function countArchived() {
        $sql = "SELECT COUNT(*) as total FROM annonces WHERE is_deleted = 1";
        return $this->db->query($sql)->fetch()['total'];
    }

    // Jib lina aakhir 3 dyl les offres m3a smyt charika
    public function getRecent($limit = 3) {
        $sql = "SELECT a.*, e.nom as entreprise_nom 
                FROM annonces a 
                JOIN entreprises e ON a.entreprise_id = e.id 
                WHERE a.is_deleted = 0 
                ORDER BY a.date_creation DESC 
                LIMIT $limit";
        return $this->db->query($sql)->fetchAll();
    }
}