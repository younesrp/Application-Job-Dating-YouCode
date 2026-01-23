<?php

namespace App\controllers\front;

use App\core\Controller;
use App\models\Candidature;
use App\models\Student;

class CandidatureController extends Controller
{
    private const CV_UPLOAD_DIR = '/public/uploads/cvs/';
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5 MB

    /**
     * Soumettre une candidature
     */
    public function apply()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/dashboard');
        }

        // Vérifier le token CSRF
        if (!isset($_POST['_token']) || $_POST['_token'] !== $this->session->get('csrf_token')) {
            $this->session->flash('errors', ['general' => ['Token de sécurité invalide']]);
            $this->redirect('/dashboard');
        }

        $userId = $this->session->get('user_id');
        $annonceId = isset($_POST['annonce_id']) ? (int)$_POST['annonce_id'] : null;
        $message = isset($_POST['message']) ? trim($_POST['message']) : null;

        // Valider les données
        $errors = $this->validateCandidatureData($userId, $annonceId, $message);

        if (!empty($errors)) {
            $this->session->flash('errors', $errors);
            $this->redirect('/dashboard');
        }

        // Récupérer l'ID apprenant
        $studentModel = new Student();
        $student = $studentModel->findByUserId($userId);

        if (!$student) {
            $this->session->flash('errors', ['general' => ['Profil apprenant non trouvé']]);
            $this->redirect('/dashboard');
        }

        // Vérifier si l'apprenant a une candidature acceptée
        $candidatureModel = new Candidature();
        $acceptedApplication = $candidatureModel->hasAcceptedApplication($userId);
        
        if ($acceptedApplication) {
            $this->session->flash('errors', ['general' => ['Vous avez déjà accepté une offre. Vous ne pouvez plus postuler à d\'autres offres.']]);
            $this->redirect('/dashboard');
        }

        // Vérifier si l'apprenant a déjà postulé
        if ($candidatureModel->hasApplied($userId, $annonceId)) {
            $this->session->flash('errors', ['general' => ['Vous avez déjà postulé à cette offre']]);
            $this->redirect('/dashboard');
        }

        // Créer la candidature
        $success = $candidatureModel->createCandidature($userId, $annonceId, $message, null);

        if ($success) {
            $this->session->flash('success', 'Candidature envoyée avec succès!');
        } else {
            $this->session->flash('errors', ['general' => ['Erreur lors de l\'envoi de la candidature']]);
        }

        $this->redirect('/dashboard');
    }

    /**
     * Valider les données de candidature
     */
    private function validateCandidatureData($userId, $annonceId, $message)
    {
        $errors = [];

        if (!$userId) {
            $errors['general'] = ['Vous devez être connecté'];
        }

        if (!$annonceId || $annonceId <= 0) {
            $errors['annonce_id'] = ['Offre invalide'];
        }

        if (!$message || strlen($message) < 50) {
            $errors['message'] = ['Le message doit contenir au minimum 50 caractères'];
        }

        if (strlen($message) > 2000) {
            $errors['message'] = ['Le message ne doit pas dépasser 2000 caractères'];
        }

        return $errors;
    }
}
