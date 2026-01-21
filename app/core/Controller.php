<?php

namespace App\core;
use App\core\{View,Validator,Security,Session};
class Controller
{

use App\core\View;
use App\core\Validator;
use App\core\Security;
use App\core\Session;

class Controller
{
    protected $view;
    protected $security;
    protected $session;
    protected $validator;

    public function __construct()
    {
        // هنا قمنا بإنشاء Instance من View
        $this->view = new View(); 
        $this->security = new Security();
        $this->session = Session::getInstance();
        $this->validator = new Validator();
    }

    /**
     * دالة لعرض صفحات Twig
     */
    protected function render(string $view, array $data = []): void
    {
        // ✅ التصحيح: نستعمل $this->view لأنها ليست Static
        $this->view->render($view, $data); 
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
        require $viewPath;
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