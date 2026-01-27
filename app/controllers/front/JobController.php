<?php

namespace App\controllers\front;

use App\core\Controller;
use App\models\Announcement;
use App\models\Candidature;

class JobController extends Controller
{
    private const ITEMS_PER_PAGE = 6;

    /**
     * Affiche la liste de toutes les annonces avec pagination
     */
    public function index()
    {
        $annonceModel = new Announcement();
        $candidatureModel = new Candidature();
        $userId = $this->session->get('user_id');

        // Générer un token CSRF
        if (!$this->session->get('csrf_token')) {
            $token = bin2hex(random_bytes(32));
            $this->session->set('csrf_token', $token);
        }

        // Récupérer le numéro de page
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

        // Récupérer toutes les annonces
        $allAnnonces = $annonceModel->getAll();
        $totalAnnonces = count($allAnnonces);

        // Calculer la pagination
        $totalPages = ceil($totalAnnonces / self::ITEMS_PER_PAGE);
        $page = min($page, max(1, $totalPages)); // S'assurer que la page est valide

        // Paginer les annonces
        $offset = ($page - 1) * self::ITEMS_PER_PAGE;
        $annonces = array_slice($allAnnonces, $offset, self::ITEMS_PER_PAGE);

        // Récupérer les candidatures de l'apprenant
        $myApplications = $candidatureModel->getByApprenant($userId);

        // Vérifier si l'apprenant a une candidature acceptée
        $acceptedApplication = $candidatureModel->hasAcceptedApplication($userId);

        // Récupérer les messages flash
        $flashSuccess = $this->session->getFlash('success');
        $flashError = $this->session->getFlash('error');
        $flashErrors = $this->session->getFlash('errors') ?? [];

        $data = [
            'title' => 'Offres d\'Emploi',
            'user' => [
                'name' => $this->session->get('user_name'),
                'email' => $this->session->get('user_email'),
                'role' => $this->session->get('user_role'),
                'id' => $userId
            ],
            'annonces' => $annonces,
            'my_applications' => $myApplications,
            'accepted_application' => $acceptedApplication,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalAnnonces,
                'items_per_page' => self::ITEMS_PER_PAGE,
                'has_previous' => $page > 1,
                'has_next' => $page < $totalPages,
                'previous_page' => $page - 1,
                'next_page' => $page + 1
            ],
            'csrf_token' => $this->session->get('csrf_token'),
            'flash_success' => $flashSuccess,
            'flash_error' => $flashError,
            'flash_errors' => $flashErrors
        ];

        $this->render('front/offres/index', $data);
    }

    /**
     * Affiche le détail d'une annonce
     */
    public function detail()
    {
        $annonceModel = new Announcement();
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect('/offres');
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

    /**
     * Recherche AJAX des annonces
     */
    public function search()
    {
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        $query = trim($data['query'] ?? '');
        
        if (strlen($query) < 2) {
            echo json_encode(['annonces' => []]);
            return;
        }
        
        $annonceModel = new Announcement();
        $allAnnonces = $annonceModel->getAll();
        
        $results = array_filter($allAnnonces, function($annonce) use ($query) {
            $searchIn = strtolower(
                $annonce['titre'] . ' ' . 
                $annonce['entreprise_nom'] . ' ' . 
                $annonce['description'] . ' ' . 
                ($annonce['competences'] ?? '')
            );
            return strpos($searchIn, strtolower($query)) !== false;
        });
        
        echo json_encode(['annonces' => array_values($results)]);
    }
}
