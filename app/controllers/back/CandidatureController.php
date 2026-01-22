<?php

namespace App\controllers\back;

use App\core\Controller;
use App\models\Candidature;

class CandidatureController extends Controller 
{
    public function index() 
    {
        $candidatureModel = new Candidature();
        $candidatures = $candidatureModel->getAllCandidatures();

        $this->render('back/candidatures/index', [
            'candidatures' => $candidatures
        ]);
    }

public function updateStatus($id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        if (isset($_POST['statut'])) {
            $status = $_POST['statut'];
            
            $candidatureModel = new Candidature();
            $candidatureModel->updateStatus($id, $status);
        }

        header('Location: /admin/candidatures');
        exit;
    }
}
}