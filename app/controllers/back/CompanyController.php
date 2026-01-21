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

    /**
     * Afficher le formulaire d'ajout d'entreprise
     */
    public function company()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->render('back/companies/index', [
                'title' => 'Ajouter une Entreprise',
                'csrf_token' => $this->security->generateCsrfToken(),
                'errors' => $this->session->flash('errors'),
                'success' => $this->session->flash('success')
            ]);
        }

        // POST - Traitement du formulaire
        return $this->store();
    }

    /**
     * Enregistrer une nouvelle entreprise
     */
    private function store()
    {
        // 1. Vérifier CSRF Token
        if (!$this->verifyCsrf()) {
            $this->session->flash('errors', ['csrf' => 'Token CSRF invalide']);
            return $this->redirect('/Company');
        }

        // 2. Récupérer et nettoyer les données
        $nom = htmlspecialchars(trim($_POST['Nom'] ?? ''), ENT_QUOTES, 'UTF-8');
        $secteur = htmlspecialchars(trim($_POST['Secteur'] ?? ''), ENT_QUOTES, 'UTF-8');
        $ville = htmlspecialchars(trim($_POST['Ville'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $telephone = htmlspecialchars(trim($_POST['telephone'] ?? ''), ENT_QUOTES, 'UTF-8');

        // 3. Validation des champs
        $errors = [];

        if (empty($nom)) {
            $errors['Nom'] = "Le nom de l'entreprise est obligatoire";
        }

        if (empty($secteur)) {
            $errors['Secteur'] = "Le secteur d'activité est obligatoire";
        }

        if (empty($ville)) {
            $errors['Ville'] = "La ville est obligatoire";
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email invalide";
        }

        if (empty($telephone)) {
            $errors['telephone'] = "Le téléphone est obligatoire";
        }

        // 4. Vérifier si l'email existe déjà
        if (empty($errors['email'])) {
            try {
                $companyModel = new Company();
                $existingCompany = $companyModel->findByEmail($email);
                
                if ($existingCompany) {
                    $errors['email'] = "Cet email est déjà utilisé";
                }
            } catch (\Exception $e) {
                $errors['database'] = "Erreur lors de la vérification: " . $e->getMessage();
            }
        }

        // 5. Gérer l'upload du logo (sécurisé)
        $logoPath = null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->handleLogoUpload($_FILES['logo']);
            
            if ($uploadResult['success']) {
                $logoPath = $uploadResult['path'];
            } else {
                $errors['logo'] = $uploadResult['error'];
            }
        }

        // 6. Si erreurs, retourner au formulaire
        if (!empty($errors)) {
            $this->session->flash('errors', $errors);
            return $this->redirect('/Company');
        }

        // 7. Enregistrer dans la base de données
        try {
            $companyModel = new Company();
            $result = $companyModel->create([
                'nom' => $nom,
                'secteur' => $secteur,
                'ville' => $ville,
                'email' => $email,
                'telephone' => $telephone,
                'logo' => $logoPath
            ]);

            if ($result) {
                $this->session->flash('success', 'Entreprise ajoutée avec succès!');
                return $this->redirect('/dashboard');
            } else {
                throw new \Exception("Erreur lors de l'enregistrement");
            }
        } catch (\Exception $e) {
            $this->session->flash('errors', ['database' => $e->getMessage()]);
            return $this->redirect('/Company');
        }
    }

    /**
     * Gérer l'upload sécurisé du logo
     */
    private function handleLogoUpload($file)
    {
        // Vérifications de sécurité
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        // Vérifier le type MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'error' => 'Type de fichier non autorisé'];
        }

        // Vérifier la taille
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Fichier trop volumineux (max 5MB)'];
        }

        // Générer un nom unique et sécurisé
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('logo_', true) . '.' . $extension;
        $uploadDir = __DIR__ . '/../../../public/uploads/logos/';
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $destination = $uploadDir . $filename;

        // Déplacer le fichier
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'path' => '/uploads/logos/' . $filename];
        }

        return ['success' => false, 'error' => 'Erreur lors du téléchargement'];
    }
}