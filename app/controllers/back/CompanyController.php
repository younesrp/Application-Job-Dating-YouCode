<?php

namespace App\controllers\back;

use App\core\Controller;
use App\models\Company;

class CompanyController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $companyModel = new Company();
        $companies = $companyModel->getAll();

        $this->render('back/companies/index', [
            'companies' => $companies,
            'title' => 'Liste des Entreprises'
        ]);
    }

    public function create()
    {
        $this->render('back/companies/create', [
            'title' => 'Ajouter une entreprise'
        ]);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Validation
            $isValid = $this->validator->validate($_POST, [
                'nom' => "Le nom est obligatoire",
                'email' => "L'email est obligatoire",
                'ville' => "La ville est obligatoire"
            ]);

            if (!$isValid) {
                $this->session->set('errors', $this->validator->errors());
                header('Location: /admin/companies/create');
                exit;
            }

            // Sanitization
            $nom = htmlspecialchars($_POST['nom']);
            $secteur = htmlspecialchars($_POST['secteur']);
            $ville = htmlspecialchars($_POST['ville']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $telephone = htmlspecialchars($_POST['telephone']);
            
            // Upload Logo
            $logoName = 'default.png'; 
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                $logoName = $this->uploadImage($_FILES['logo']);
            }

            // Save
            $companyModel = new Company();
            $result = $companyModel->create([
                ':nom' => $nom,
                ':secteur' => $secteur,
                ':ville' => $ville,
                ':email' => $email,
                ':telephone' => $telephone,
                ':logo' => $logoName
            ]);

            if ($result) {
                $this->session->set('success', 'Entreprise ajoutée avec succès !');
                header('Location: /admin/companies');
            } else {
                $this->session->set('errors', ['global' => "Erreur lors de l'enregistrement"]);
                header('Location: /admin/companies/create');
            }
            exit;
        }
    }

    public function edit($id)
    {
        $companyModel = new Company();
        $company = $companyModel->find($id);

        if (!$company) {
            header('Location: /admin/companies');
            exit;
        }

        $this->render('back/companies/edit', [
            'company' => $company,
            'title' => 'Modifier l\'entreprise'
        ]);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $companyModel = new Company();
            
            $currentCompany = $companyModel->find($id); 

            if (!$currentCompany) {
                header('Location: /admin/companies');
                exit;
            }
            
            $nom = htmlspecialchars($_POST['nom']);
            $secteur = htmlspecialchars($_POST['secteur']);
            $ville = htmlspecialchars($_POST['ville']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $telephone = htmlspecialchars($_POST['telephone']);

            $logoName = $currentCompany->logo; 

            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                $logoName = $this->uploadImage($_FILES['logo']);
            }

            $data = [
                ':nom' => $nom,
                ':secteur' => $secteur,
                ':ville' => $ville,
                ':email' => $email,
                ':telephone' => $telephone,
                ':logo' => $logoName // ✅ ضروري تكون زايـد هادي فالمودل
            ];

            $companyModel->update($id, $data);
            
            $this->session->set('success', 'Entreprise mise à jour !');
            header('Location: /admin/companies');
            exit;
        }
    }

    public function delete($id)
    {
        $companyModel = new Company();
        $companyModel->delete($id);
        
        $this->session->set('success', 'Entreprise supprimée !');
        header('Location: /admin/companies');
        exit;
    }

    private function uploadImage($file)
    {
        $targetDir = __DIR__ . "/../../../public/assets/logos/";
        
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension;
        $targetFilePath = $targetDir . $fileName;

        if(move_uploaded_file($file['tmp_name'], $targetFilePath)){
            return $fileName;
        }
        
        return 'default.png';
    }
}