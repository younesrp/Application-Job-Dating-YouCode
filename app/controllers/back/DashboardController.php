<?php

namespace App\controllers\back;

use App\core\Controller; 
use App\models\Announcement;
use App\models\Company;
use App\models\Student;

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
}