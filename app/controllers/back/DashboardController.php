<?php

namespace App\controllers\back;

use App\core\Controller; 
use App\models\Announcement;
use App\models\Company;
use App\models\Student;
use App\models\Candidature;

class DashboardController extends Controller { 

    public function index() {
        $annonceModel = new Announcement();
        $entrepriseModel = new Company();
        $studentModel = new Student();

        $stats = [
            'active_annonces'   => $annonceModel->countActive(),
            'archived_annonces' => $annonceModel->countArchived(),
            'entreprises'       => $entrepriseModel->countAll(),
            'students'          => $studentModel->countAll()
        ];

        $recentAds = $annonceModel->getRecent(3);

        $this->render('back/dashboard/index', [
            'stats' => $stats,
            'recent_ads' => $recentAds
        ]);
    }

    
    public function candidatures()
    {
        $candidatureModel = new Candidature();
        
        $candidatures = $candidatureModel->getAllCandidaturesDetailed();
        
        // Calculer les stats
        $stats = [
            'total' => count($candidatures),
            'en_attente' => count(array_filter($candidatures, fn($c) => $c['statut'] === 'en_attente')),
            'validee' => count(array_filter($candidatures, fn($c) => $c['statut'] === 'validee')),
            'refusee' => count(array_filter($candidatures, fn($c) => $c['statut'] === 'refusee'))
        ];
        
        $this->render('back/candidatures/index', [
            'candidatures' => $candidatures,
            'stats' => $stats
        ]);
    }

    /**
     * Met à jour le statut d'une candidature (AJAX)
     */
    public function updateCandidatureStatus($candidatureId = null)
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }

        try {
            // L'ID vient du paramètre de route
            if (!$candidatureId) {
                $candidatureId = $_GET['id'] ?? null;
            }
            
            // Récupérer les données JSON
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            $newStatus = $data['statut'] ?? null;

            // Vérifier les paramètres
            if (!$candidatureId || !$newStatus) {
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Paramètres invalides',
                    'debug' => [
                        'id' => $candidatureId,
                        'status' => $newStatus,
                        'input' => $input
                    ]
                ]);
                exit;
            }

            // Mettre à jour le statut
            $candidatureModel = new Candidature();
            $result = $candidatureModel->updateStatus($candidatureId, $newStatus);

            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Statut mis à jour avec succès']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Servir un CV en tant que fichier (pour PDFjs) - Route publique
     */
    public function serveCv($filename = null)
    {
        if (!$filename) {
            http_response_code(404);
            echo 'Fichier non trouvé';
            exit;
        }

        // Sécurité: vérifier que le filename ne contient que des caractères autorisés
        if (!preg_match('/^candidature_\d+_\d+_\d+\.pdf$/', $filename)) {
            http_response_code(403);
            echo 'Accès refusé';
            exit;
        }

        // Utiliser __DIR__ pour construire le chemin absolu
        $filepath = __DIR__ . '/../../public/uploads/cvs/' . $filename;

        if (!file_exists($filepath)) {
            http_response_code(404);
            echo 'Fichier non trouvé';
            exit;
        }

        // Servir le fichier PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($filename) . '"');
        header('Content-Length: ' . filesize($filepath));
        header('Cache-Control: public, max-age=3600');
        header('Access-Control-Allow-Origin: *');
        readfile($filepath);
        exit;
    }

    /**
     * Servir un CV en tant que fichier (pour PDFjs)
     */
    public function downloadCv($filename = null)
    {
        return $this->serveCv($filename);
    }
}