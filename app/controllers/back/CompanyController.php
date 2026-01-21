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

        // ✅ Validation avec les nouvelles méthodes de sécurité
        $isValid = $this->validator->validate($_POST, [
            'email' => 'required|email',
            'password' => 'required|password'
        ]);

        if (!$isValid) {
            $this->session->flash('errors', $this->validator->errors());
            $this->redirect('/login');
        }

        // ✅ Nettoyer les données avant utilisation
        $sanitizedData = $this->validator->sanitize($_POST, [
            'email' => 'email',
            'password' => 'string'
        ]);

        $email = $sanitizedData['email'];
        $password = $sanitizedData['password'];

        // Vérifier les identifiants (exemple simplifié)
        // En production, utiliser des prepared statements avec PDO
        try {
            // Nettoyage des paramètres contre injection SQL
            $params = $this->security->preventSQLInjection(
                "SELECT * FROM users WHERE email = ?",
                [$email]
            );
            
            // Utiliser $params[0] pour la requête préparée
            // $user = User::where('email', $params[0])->first();
            
            $this->session->flash('success', 'Connexion réussie');
            $this->redirect('/dashboard');
        } catch (\Exception $e) {
            $this->session->flash('errors', ['email' => [$e->getMessage()]]);
            $this->redirect('/login');
        }
    } 
}