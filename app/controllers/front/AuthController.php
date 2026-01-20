<?php
namespace App\controllers;
use App\core\{BaseController,Security,Session,Validator};

// use App\models\User;

class AuthController extends BaseController
{
 
    public function __construct()
    {
        parent::__construct();
    }

    public function index(){
         $this->render('index');
    }

    public function showRegister()
    {
        $data = [
            'title' => 'Connexion',
            'csrf_token' => $this->security->generateCsrfToken(),
            'errors'  => $this->session->flash('errors'),
            'success' => $this->session->flash('success'),

        ];
        // الكود ديال صفحة التسجيل
        $this->render('Auth/register', $data);
    }
    /**
     * Gère register (GET et POST)
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $data = [
                'title' => 'Inscription',
                'csrf_token' => $this->security->generateCsrfToken(),
            ];
            return $this->render('Auth/register', $data);
        }
        
        $this->verifyCsrf();
        
        /** TODO: Traiter l'inscription*/
    }


    /**
     * Affiche le formulaire de connexion
     */
    public function showLogin()
    {
       

        $data = [
            'title' => 'Connexion',
            'csrf_token' => $this->security->generateCsrfToken(),
            'errors'  => $this->session->flash('errors'),
            'success' => $this->session->flash('success'),

        ];

        $this->render('Auth/login', $data);
    }

    /**
     * Traite la connexion
     * Gère login (GET et POST)
     */
    public function login()
    {
        // GET → Afficher le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $data = [
                'title' => 'Connexion',
                'csrf_token' => $this->security->generateCsrfToken(),
                'errors'  => $this->session->flash('errors'),
                'success' => $this->session->flash('success'),
            ];
            
            return $this->render('Auth/login', $data);
        }
        
        // POST → Traiter la connexion
        $this->verifyCsrf();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

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
    
    
    public function dashboard()
    {
        $data = ['title' => 'Dashboard'];
        $this->render('dashboard', $data);
    }
}