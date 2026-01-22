<?php

namespace App\models;

use App\core\Model;

class Candidature extends Model {
    
    protected $table = 'candidatures';

    public function getAllCandidatures() {
        // ✅ كويري مركبة: Candidature + Apprenant + Annonce
        $sql = "SELECT 
                    c.id, 
                    c.date_candidature, 
                    c.statut, 
                    ap.nom AS apprenant_nom, 
                    ap.prenom AS apprenant_prenom,
                    an.titre AS annonce_titre,
                    e.nom AS entreprise_nom
                FROM candidatures c
                JOIN apprenants ap ON c.apprenant_id = ap.user_id
                JOIN annonces an ON c.annonce_id = an.id
                JOIN entreprises e ON an.entreprise_id = e.id
                ORDER BY c.date_candidature DESC";
                
        return $this->pdo->query($sql)->fetchAll();
    }
    public function updateStatus($id, $status) {
    // كنبدلو الـ statut ديال الترشيح بناءً على الـ id
    $sql = "UPDATE candidatures SET statut = :statut WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    
    return $stmt->execute([
        ':statut' => $status,
        ':id' => $id
    ]);
}
}