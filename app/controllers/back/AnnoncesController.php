<?php

namespace App\controllers\back;

use App\core\Controller;
use App\models\Announcement;
use App\models\Company; 

class AnnoncesController extends Controller 
{
    public function index() 
    {
        $annonceModel = new Announcement();
        $annonces = $annonceModel->getAll(); 
        
        $this->render('back/annonces/index', [
            'annonces' => $annonces
        ]);
    }

    public function create()
    {
        $companyModel = new Company();
        $companies = $companyModel->getAll(); 

        $this->render('back/annonces/create', [
            'companies' => $companies 
        ]);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $data = [
                'titre' => $_POST['titre'],
                'description' => $_POST['description'],
                'entreprise_id' => $_POST['entreprise_id'] 
            ];

            $annonceModel = new Announcement();
            
            if ($annonceModel->create($data)) {
                header('Location: /admin/annonces');
                exit;
            } else {
                die("Erreur lors de l'enregistrement");
            }
        }
    }

public function delete($id) {
    $annonceModel = new Announcement();
    
    if ($annonceModel->delete($id)) {
        header('Location: /admin/annonces');
        exit;
    } else {
        die("Erreur lors de la suppression");
    }
}


public function edit($id)
{
    $annonceModel = new Announcement();
    $companyModel = new Company();

    $annonce = $annonceModel->find($id);

    $companies = $companyModel->getAll();

    if (!$annonce) {
        die("Annonce introuvable");
    }

    $this->render('back/annonces/edit', [
        'annonce' => $annonce,
        'companies' => $companies
    ]);
}

public function update($id)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $data = [
            'titre' => $_POST['titre'],
            'description' => $_POST['description'],
            'entreprise_id' => $_POST['entreprise_id'],
            'is_deleted' => $_POST['status']
        ];

        $annonceModel = new Announcement();

        if ($annonceModel->update($id, $data)) {
            header('Location: /admin/annonces');
            exit;
        } else {
            die("Erreur de mise à jour");
        }
    }
}
}