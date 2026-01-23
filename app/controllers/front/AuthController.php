<?php

namespace App\controllers\front;

use App\core\{Controller, Security, Session, Validator};
// Assurez-vous que le modèle User est bien importé
use App\models\User;

class AuthController extends Controller
{
    /**
     * @var User
     */
    private $userModel; // 1. La propriété est déclarée ici

    /**
     * Constructeur : initialise les dépendances
     */
    public function __construct()
    {
        parent::__construct();
        // 2. La propriété est initialisée ici. C'est la ligne qui manquait !
        $this->userModel = new User();
    }

    /**
     * Affiche la page de connexion
     */
    public function showLogin()
    {
        $data = [
            'title' => 'Connexion',
            'csrf_token' => $this->security->generateCsrfToken(),
            'errors' => $this->session->flash('errors'),
            'success' => $this->session->flash('success'),
        ];

        $this->render('front/auth/login', $data);
    }

    /**
     * Traite la soumission du formulaire de connexion
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->showLogin();
        }

        $this->verifyCsrf();

        // Validation
        $isValid = $this->validator->validate($_POST, [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if (!$isValid) {
            $this->session->flash('errors', $this->validator->errors());
            $this->redirect('/login');
        }

        // Sanitization
        $sanitized = $this->validator->sanitize($_POST, [
            'email' => 'email',
            'password' => 'string'
        ]);

        // Vérification si c'est un admin hardcodé
        if ($this->userModel->isAdminEmail($sanitized['email'])) {
            // Créer/mettre à jour l'admin s'il n'existe pas
            $existingUser = $this->userModel->findByEmail($sanitized['email']);
            if (!$existingUser) {
                // Créer seulement si l'utilisateur n'existe pas
                $this->userModel->createOrUpdateAdmin([
                    'email' => $sanitized['email'],
                    'password' => $sanitized['password'],
                    'prenom' => 'Admin', // Valeurs par défaut pour l'admin
                    'nom' => 'System'
                ]);
            }
        }

        // Authentification
        $user = $this->userModel->authenticate($sanitized['email'], $sanitized['password']);

        if (!$user) {
            $this->session->flash('errors', ['email' => ['Email ou mot de passe incorrect']]);
            $this->redirect('/login');
        }

        // Connexion réussie - création de session
        $this->session->set('user_id', $user['id']);
        $this->session->set('user_email', $user['email']);
        $this->session->set('user_role', $user['role']);
        // On combine prénom et nom pour l'affichage
        $this->session->set('user_name', trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')));

        // Redirection selon le rôle
        if ($user['role'] === User::ROLE_ADMIN) {
            $this->session->flash('success', 'Bienvenue administrateur !');
            $this->redirect('/admin/dashboard');
        } else {
            $this->session->flash('success', 'Connexion réussie !');
            $this->redirect('/dashboard');
        }
    }

    /**
     * Affiche la page d'inscription
     */
    public function showRegister()
    {
        $data = [
            'title' => 'Inscription',
            'csrf_token' => $this->security->generateCsrfToken(),
            'errors' => $this->session->flash('errors'),
            'success' => $this->session->flash('success'),
        ];

        $this->render('front/auth/register', $data);
    }

    /**
     * Traite la soumission du formulaire d'inscription
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->showRegister();
        }

        $this->verifyCsrf();

        // Validation
        $isValid = $this->validator->validate($_POST, [
            'prenom' => 'required|min:2',
            'nom' => 'required|min:2',
            'email' => 'required|email',
            'telephone' => 'required',
            'promotion' => 'string',
            'specialisation' => 'string',
            'password' => 'required|min:6|confirmed' // 'confirmed' vérifie le champ 'password_confirmation'
        ]);

        if (!$isValid) {
            $this->session->flash('errors', $this->validator->errors());
            $this->redirect('/register');
        }

        // Sanitization
        $sanitized = $this->validator->sanitize($_POST, [
            'prenom' => 'string',
            'nom' => 'string',
            'email' => 'email',
            'telephone' => 'string',
            'promotion' => 'string',
            'specialisation' => 'string',
            'password' => 'string'
        ]);

        // Vérifier si l'email existe déjà
        if ($this->userModel->emailExists($sanitized['email'])) {
            $this->session->flash('errors', ['email' => ['Cet email est déjà utilisé']]);
            $this->redirect('/register');
        }

        // Vérifier si c'est un email admin (interdit en inscription normale)
        if ($this->userModel->isAdminEmail($sanitized['email'])) {
            $this->session->flash('errors', ['email' => ['Cet email est réservé aux administrateurs']]);
            $this->redirect('/register');
        }

        // Création de l'apprenant
        $userId = $this->userModel->createApprenant([
            'prenom' => $sanitized['prenom'],
            'nom' => $sanitized['nom'],
            'email' => $sanitized['email'],
            'telephone' => $sanitized['telephone'],
            'password' => $sanitized['password']
        ]);

        if ($userId) {
            $this->session->flash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter');
            $this->redirect('/login');
        } else {
            $this->session->flash('errors', ['general' => ['Erreur lors de l\'inscription']]);
            $this->redirect('/register');
        }
    }

    /**
     * Déconnecte l'utilisateur
     */
    public function logout()
    {
        $this->session->destroy();
        $this->redirect('/login');
    }
}