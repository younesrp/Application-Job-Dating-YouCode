<?php

namespace App\Controllers\Back;

use App\Core\Controller;
use App\Models\Announcement;
use App\Models\Company;
use App\Models\Student;

class DashboardController extends Controller {

    public function index() {
        // 1. Instancier les modèles
        $annonceModel = new Announcement();
        $entrepriseModel = new Company();
        $studentModel = new Student();

        // 2. Récupérer les statistiques
        $stats = [
            'active_annonces'   => $annonceModel->countActive(),
            'archived_annonces' => $annonceModel->countArchived(),
            'entreprises'       => $entrepriseModel->countAll(),
            'students'          => $studentModel->countAll()
        ];

        // 3. Récupérer les offres récentes
        $recentAds = $annonceModel->getRecent(3);

        // 4. Envoyer à la vue
        $this->view('back/dashboard/index', [
            'stats' => $stats,
            'recent_ads' => $recentAds
        ]);
    }
}