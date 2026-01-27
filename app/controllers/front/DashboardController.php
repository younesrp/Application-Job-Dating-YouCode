<?php

namespace App\controllers\front;

use App\core\Controller;
use App\models\Announcement;
use App\models\Candidature;
use App\models\Company;
use App\models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $annonceModel = new Announcement();
        $candidatureModel = new Candidature();
        $companyModel = new Company();
        $userId = $this->session->get('user_id');
        
        // Générer un token CSRF
        if (!$this->session->get('csrf_token')) {
            $token = bin2hex(random_bytes(32));
            $this->session->set('csrf_token', $token);
        }
        
        // Récupérer toutes les annonces actives
        $allAnnonces = $annonceModel->getAll();
        
        // Récupérer les candidatures de l'apprenant
        $myApplications = $candidatureModel->getByApprenant($userId);
        
        // Vérifier si l'apprenant a une candidature acceptée
        $acceptedApplication = $candidatureModel->hasAcceptedApplication($userId);
        
        // Compter les statistiques
        $stats = [
            'recommended_jobs' => count($allAnnonces),
            'applications_sent' => count($myApplications),
            'followed_companies' => $companyModel->countAll(),
            'interviews_scheduled' => 0 // À mettre à jour selon votre logique
        ];
        
        // Récupérer les messages flash
        $flashSuccess = $this->session->getFlash('success');
        $flashError = $this->session->getFlash('error');
        $flashErrors = $this->session->getFlash('errors') ?? [];
        
        $data = [
            'title' => 'Dashboard Apprenant',
            'user' => [
                'name' => $this->session->get('user_name'),
                'email' => $this->session->get('user_email'),
                'role' => $this->session->get('user_role'),
                'id' => $userId
            ],
            'stats' => $stats,
            'annonces' => $allAnnonces,
            'my_applications' => $myApplications,
            'accepted_application' => $acceptedApplication,
            'csrf_token' => $this->session->get('csrf_token'),
            'flash_success' => $flashSuccess,
            'flash_error' => $flashError,
            'flash_errors' => $flashErrors
        ];
        
        $this->render('front/dashboard/index', $data);
    }

    /**
     * Affiche la page des candidatures
     */
    public function candidatures()
    {
        $candidatureModel = new Candidature();
        $userId = $this->session->get('user_id');
        
        // Récupérer les candidatures de l'apprenant
        $myApplications = $candidatureModel->getByApprenant($userId);
        
        $data = [
            'title' => 'Mes Candidatures',
            'user' => [
                'name' => $this->session->get('user_name'),
                'email' => $this->session->get('user_email'),
                'role' => $this->session->get('user_role')
            ],
            'my_applications' => $myApplications
        ];
        
        $this->render('front/candidatures/index', $data);
    }

    /**
     * Affiche la page du profil
     */
    public function profil()
    {
        $studentModel = new Student();
        $userId = $this->session->get('user_id');
        
        // Générer un token CSRF
        if (!$this->session->get('csrf_token')) {
            $token = bin2hex(random_bytes(32));
            $this->session->set('csrf_token', $token);
        }
        
        // Récupérer les informations complètes de l'apprenant
        $student = $studentModel->findByUserId($userId);
        
        $data = [
            'title' => 'Mon Profil',
            'user' => [
                'name' => $this->session->get('user_name'),
                'email' => $this->session->get('user_email'),
                'role' => $this->session->get('user_role'),
                'id' => $userId
            ],
            'student' => $student,
            'csrf_token' => $this->session->get('csrf_token'),
            'flash_success' => $this->session->getFlash('success'),
            'flash_errors' => $this->session->getFlash('errors') ?? []
        ];
        
        $this->render('front/profil/index', $data);
    }

    /**
     * Met à jour le profil de l'apprenant
     */
    public function updateProfil()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/profil');
        }

        // Vérifier le token CSRF
        if (!isset($_POST['_token']) || $_POST['_token'] !== $this->session->get('csrf_token')) {
            $this->session->flash('errors', ['general' => ['Token de sécurité invalide']]);
            $this->redirect('/profil');
        }

        $userId = $this->session->get('user_id');
        $studentModel = new Student();

        // Préparer les données à mettre à jour
        $data = [
            'nom' => isset($_POST['nom']) ? trim($_POST['nom']) : null,
            'prenom' => isset($_POST['prenom']) ? trim($_POST['prenom']) : null,
            'promotion' => isset($_POST['promotion']) ? trim($_POST['promotion']) : null,
            'specialisation' => isset($_POST['specialisation']) ? trim($_POST['specialisation']) : null
        ];

        // Valider les données
        $errors = [];
        if (empty($data['nom'])) {
            $errors['nom'] = ['Le nom est requis'];
        }
        if (empty($data['prenom'])) {
            $errors['prenom'] = ['Le prénom est requis'];
        }

        if (!empty($errors)) {
            $this->session->flash('errors', $errors);
            $this->redirect('/profil');
        }

        // Mettre à jour le profil
        if ($studentModel->updateStudent($userId, $data)) {
            // Mettre à jour le nom en session
            $fullName = $data['prenom'] . ' ' . $data['nom'];
            $this->session->set('user_name', $fullName);
            
            $this->session->flash('success', 'Profil mis à jour avec succès!');
        } else {
            $this->session->flash('errors', ['general' => ['Erreur lors de la mise à jour du profil']]);
        }

        $this->redirect('/profil');
    }

    /**
     * Affiche le détail d'une offre
     */
    public function detailOffre()
    {
        $annonceModel = new Announcement();
        $id = $_GET['id'] ?? null;
        
        if (!$id) {
            $this->redirect('/dashboard');
        }
        
        $annonce = $annonceModel->find($id);
        
        if (!$annonce) {
            http_response_code(404);
            $this->render('front/404', [
                'title' => 'Page non trouvée'
            ]);
            return;
        }
        
        $data = [
            'title' => $annonce['titre'] ?? 'Détail Offre',
            'user' => [
                'name' => $this->session->get('user_name'),
                'email' => $this->session->get('user_email'),
                'role' => $this->session->get('user_role')
            ],
            'annonce' => $annonce
        ];
        
        $this->render('front/offres/detail', $data);
    }
}