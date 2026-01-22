<?php
namespace App\controllers\back;
use App\core\{Controller,Security,Session,Validator};

// use App\models\User;

class CompanyController extends Controller
{
 
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
         $this->render('index');
    }

    public function showCompany()
    {
        $data = [
            'title' => 'Ajouter',
            'csrf_token' => $this->security->generateCsrfToken(),
            'errors'  => $this->session->flash('errors'),
            'success' => $this->session->flash('success'),

        ];
        
        $this->render('back/companies/index', $data);
    }
    /**
     * Gère register (GET et POST)
     */
    public function company()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $data = [
                'title' => 'Ajouter Company',
                'csrf_token' => $this->security->generateCsrfToken(),
            ];
            return $this->render('back/companies/index', $data);
        }


        // else $_SERVER['REQUEST_METHOD'] === 'POST'
        $this->verifyCsrf();
        
        /** TODO: Traiter l'ajouter de company*/
        $nom = $_POST['Nom'] ?? '';
        $secteur = $_POST['Secteur'] ?? '';
        $ville = $_POST['Ville'] ?? '';
        $email = $_POST['email'] ?? '';
        $telephone = $_POST['telephone'] ?? '';
        
        // Validation les champs
        $isValid = $this->validator->validate($_POST, [
            'Nom' => "Oblier Nom d'entreprise!",
            'Secteur' => "Oblier secteur d'entreprise!",
            'Ville' => "Oblier secteur d'entreprise!",
            'email' => "Oblier secteur d'entreprise!",
            'telephone' => "Oblier secteur d'entreprise!",
            'logo' => "Oblier image/logo d'entreprise!"
        ]);
        var_dump($isValid);
        if (!$isValid) {
            $this->session->flash('errors', $this->validator->errors());
            $this->redirect('/back');
        }

        // ✅ Nettoyer les données avant utilisation
        $sanitizedData = $this->validator->sanitize($_POST, [
            'Nom' => 'string',
            'Secteur' => 'string',
            'Ville' => 'string',
            'email' => 'email',
            'telephone' => 'integer',
            'logo' => 'url'
        ]);

        $Nom = $sanitizedData['Nom'];
        $Secteur = $sanitizedData['Secteur'];
        $Ville = $sanitizedData['Ville'];
        $email = $sanitizedData['email'];
        $telephone = $sanitizedData['telephone'];
        $logo = $sanitizedData['logo'];

        // Implémenter l'ajout avec validation d'email unique
        // En production, utiliser des prepared statements avec PDO
        try {
            // Nettoyage des paramètres contre injection SQL
            $params = $this->security->preventSQLInjection(
                "SELECT * FROM entreprises WHERE email = ?",
                [$email]
            );
            var_dump($params);
            
            $this->session->flash('success', 'Enregistre réussie');
            $this->redirect('back/companies/index');
        } catch (\Exception $e) {
            $this->session->flash('errors', ['email' => [$e->getMessage()]]);
            $this->redirect('back/companies/index');
        }
    } 
}