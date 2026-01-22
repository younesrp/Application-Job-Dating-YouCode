<?php
namespace App\controllers\back;
use App\core\Controller;
use App\models\Company;

class CompanyController extends Controller
{
    private $companyModel;

    public function __construct()
    {
        parent::__construct();
        $this->companyModel = new Company();
    }

    public function index()
    {
        $companies = $this->companyModel->getAll();
        
        $data = [
            'title' => 'Gestion des Entreprises',
            'companies' => $companies,
            'csrf_token' => $this->security->generateCsrfToken(),
            'errors' => $this->session->flash('errors'),
            'success' => $this->session->flash('success')
        ];
        
        $this->render('back/companies/index', $data);
    }

    public function store()
    {
        // Vérification CSRF
        $this->verifyCsrf();
        
        // Validation des données
        $isValid = $this->validator->validate($_POST, [
            'nom' => 'required|min:2',
            'secteur' => 'required',
            'ville' => 'required',
            'email' => 'required|email',
            'telephone' => 'required'
        ]);

        if (!$isValid) {
            $this->session->flash('errors', $this->validator->errors());
            $this->redirect('/admin/companies');
        }

        // Sanitization (nettoyage) des données
        $sanitized = $this->validator->sanitize($_POST, [
            'nom' => 'string',
            'secteur' => 'string',
            'ville' => 'string',
            'email' => 'email',
            'telephone' => 'string'
        ]);

        // Supprimer _token (ne doit pas être inséré en DB)
        unset($sanitized['_token']);

        // Vérifier l'unicité de l'email
        if ($this->companyModel->emailExists($sanitized['email'])) {
            $this->session->flash('errors', ['email' => ['Cet email existe déjà']]);
            $this->redirect('/admin/companies');
        }

        // Gestion sécurisée de l'upload du logo
        $logoPath = null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logoPath = $this->uploadLogo($_FILES['logo']);
            if (!$logoPath) {
                $this->session->flash('errors', ['logo' => ['Erreur lors de l\'upload du logo']]);
                $this->redirect('/admin/companies');
            }
        }

        $sanitized['logo'] = $logoPath;

        // Insertion en base de données avec prepared statements (protection SQL injection)
        if ($this->companyModel->create($sanitized)) {
            $this->session->flash('success', 'Entreprise ajoutée avec succès');
        } else {
            $this->session->flash('errors', ['general' => ['Erreur lors de l\'ajout']]);
        }
        
        $this->redirect('/admin/companies');
    }

    public function update($id)
    {
        $this->verifyCsrf();
        
        $isValid = $this->validator->validate($_POST, [
            'nom' => 'required|min:2',
            'secteur' => 'required',
            'ville' => 'required',
            'email' => 'required|email',
            'telephone' => 'required'
        ]);

        if (!$isValid) {
            $this->session->flash('errors', $this->validator->errors());
            $this->redirect('/admin/companies');
        }

        $sanitized = $this->validator->sanitize($_POST, [
            'nom' => 'string',
            'secteur' => 'string',
            'ville' => 'string',
            'email' => 'email',
            'telephone' => 'string'
        ]);

        // Supprimer _token s'il existe
        unset($sanitized['_token']);

        // Gestion de l'upload du logo
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $logoPath = $this->uploadLogo($_FILES['logo']);
            if ($logoPath) {
                $sanitized['logo'] = $logoPath;
            }
        }

        if ($this->companyModel->update($id, $sanitized)) {
            $this->session->flash('success', 'Entreprise modifiée avec succès');
        } else {
            $this->session->flash('errors', ['general' => ['Erreur lors de la modification']]);
        }
        
        $this->redirect('/admin/companies');
    }

    public function archive($id)
    {
        if ($this->companyModel->archive($id)) {
            $this->session->flash('success', 'Entreprise archivée');
        } else {
            $this->session->flash('errors', ['general' => ['Erreur lors de l\'archivage']]);
        }
        $this->redirect('/admin/companies');
    }

    public function restore($id)
    {
        if ($this->companyModel->restore($id)) {
            $this->session->flash('success', 'Entreprise restaurée');
        } else {
            $this->session->flash('errors', ['general' => ['Erreur lors de la restauration']]);
        }
        $this->redirect('/admin/companies');
    }

    /**
     * Upload sécurisé du logo de l'entreprise
     * @param array $file Le fichier uploadé ($_FILES['logo'])
     * @return string|false Le chemin du fichier ou false en cas d'erreur
     */
    private function uploadLogo($file)
    {
        // 1. Extensions autorisées (whitelist)
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        // 2. Vérifier la taille du fichier
        if ($file['size'] > $maxSize) {
            return false;
        }

        // 3. Vérifier l'extension (protection contre upload de fichiers malveillants)
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            return false;
        }

        // 4. Vérifier le type MIME réel du fichier
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mimeType, $allowedMimes)) {
            return false;
        }

        // 5. Générer un nom unique (protection contre l'écrasement de fichiers)
        $filename = uniqid('logo_') . '.' . $extension;
        
        // 6. Chemin absolu vers le dossier public
        $uploadDir = dirname(__DIR__, 2) . '/public/assets/images/logos/';
        
        // 7. Créer le dossier s'il n'existe pas
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $uploadPath = $uploadDir . $filename;

        // 8. Déplacer le fichier de manière sécurisée
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // 9. Retourner le chemin relatif pour la base de données
            return 'assets/images/logos/' . $filename;
        }

        return false;
    }
}