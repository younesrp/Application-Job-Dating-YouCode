<?php

namespace App\controllers\back;

// 👇 التغيير 1: عيط على Controller الموجود عندك أصلاً
use App\core\Controller; 
use App\models\Announcement;
use App\models\Company;
use App\models\Student;

// 👇 التغيير 2: ورث من Controller (ماشي BaseController)
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

        // هاد render راه ديجا كاينا وسط Controller.php ديالك
        $this->render('back/dashboard/index', [
            'stats' => $stats,
            'recent_ads' => $recentAds
        ]);
    }
}