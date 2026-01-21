<?php

namespace App\core;
use App\core\{View,Validator,Security,Session};
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
abstract class Controller
{
    protected $view;
    protected $security;
    protected $session;
    protected $validator;
    protected $twig;

    public function __construct()
    {
        $this->security = new Security();
        $this->session = Session::getInstance();
        $this->validator = new Validator();

        // Chemin correct vers le dossier views
        $viewsPath = dirname(__DIR__) . '/views';
        $loader = new FilesystemLoader($viewsPath); 
        
        $this->twig = new Environment($loader, [
            'cache' => false,
            'debug' => true
        ]);

        // Fonction asset pour les fichiers statiques
        $this->twig->addFunction(new TwigFunction('asset', function ($path) {
            $projectDir = '/Application-Job-Dating-YouCode/public'; 
            return $projectDir . '/' . ltrim($path, '/');
        }));
    }

    /**
     * 
     */
    protected function render(string $view, array $data = []): void
    {
        echo $this->twig->render($view . '.twig', $data);
    }

    /**
     * دالة لعرض صفحات PHP العادية (Legacy)
     */
    protected function view(string $view, array $data = [])
    {
        extract($data);

        $viewPath = __DIR__ . '/../views/' . $view . '.twig';

        if (!file_exists($viewPath)) {
            die("View not found: {$viewPath}");
        }
        $this->render($viewPath, $data);
    }

    protected function json(array $data, int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url, int $statusCode = 302)
    {
        error_log("Redirecting to: " . $url);
        http_response_code($statusCode);
        header("Location: $url");
        exit;
    }

    protected function verifyCsrf()
    {
        if (!$this->security->verifyCsrfToken($_POST['_token'] ?? '')) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
        }
    }
}