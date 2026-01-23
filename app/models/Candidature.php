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

    /**
     * Récupère toutes les candidatures avec infos détaillées pour l'admin
     */
    public function getAllCandidaturesDetailed() {
        $sql = "SELECT 
                    c.id,
                    c.apprenant_id,
                    c.annonce_id,
                    c.message,
                    c.cv_path,
                    c.date_candidature,
                    c.statut,
                    ap.nom AS apprenant_nom,
                    ap.prenom AS apprenant_prenom,
                    ap.promotion,
                    u.email AS apprenant_email,
                    u.telephone AS apprenant_telephone,
                    an.titre AS annonce_titre,
                    an.type_contrat,
                    e.nom AS entreprise_nom,
                    e.id AS entreprise_id
                FROM candidatures c
                JOIN apprenants ap ON c.apprenant_id = ap.user_id
                JOIN users u ON ap.user_id = u.id
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

    /**
     * Récupère les candidatures d'un apprenant spécifique
     */
    public function getByApprenant($userId) {
        $sql = "SELECT 
                    c.id, 
                    c.annonce_id, 
                    c.date_candidature, 
                    c.statut, 
                    c.message,
                    an.titre AS annonce_titre,
                    an.description,
                    e.nom AS entreprise_nom,
                    e.id AS entreprise_id
                FROM candidatures c
                JOIN annonces an ON c.annonce_id = an.id
                JOIN entreprises e ON an.entreprise_id = e.id
                JOIN apprenants ap ON c.apprenant_id = ap.user_id
                WHERE ap.user_id = :user_id
                ORDER BY c.date_candidature DESC";
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Crée une nouvelle candidature
     */
    public function createCandidature($apprentId, $annonceId, $message = null, $cvPath = null) {
        $sql = "INSERT INTO candidatures (apprenant_id, annonce_id, message, cv_path, date_candidature, statut) 
                VALUES (:apprenant_id, :annonce_id, :message, :cv_path, NOW(), 'en_attente')";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':apprenant_id' => $apprentId,
            ':annonce_id' => $annonceId,
            ':message' => $message,
            ':cv_path' => $cvPath
        ]);
    }

    /**
     * Vérifie si l'apprenant a déjà postulé à cette annonce
     */
    public function hasApplied($apprentId, $annonceId) {
        $sql = "SELECT COUNT(*) FROM candidatures 
                WHERE apprenant_id = :apprenant_id AND annonce_id = :annonce_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':apprenant_id' => $apprentId,
            ':annonce_id' => $annonceId
        ]);
        
        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Vérifie si l'apprenant a une candidature acceptée
     */
    public function hasAcceptedApplication($apprentId) {
        $sql = "SELECT * FROM candidatures 
                WHERE apprenant_id = :apprenant_id AND statut = 'validee' LIMIT 1";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':apprenant_id' => $apprentId]);
        
        return $stmt->fetch();
    }
}