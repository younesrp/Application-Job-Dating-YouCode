<?php

namespace App\controllers\front;

use App\core\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard Apprenant',
            'user' => [
                'name' => $this->session->get('user_name'),
                'email' => $this->session->get('user_email'),
                'role' => $this->session->get('user_role')
            ]
        ];
        
        $this->render('front/dashboard/index', $data);
    }
}