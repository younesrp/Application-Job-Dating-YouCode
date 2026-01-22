<?php

namespace App\controllers\front;

use App\core\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Données de statistiques pour l'apprenant
        $stats = [
            'recommended_jobs' => 12,  // À remplacer par une requête réelle
            'applications_sent' => 5,  // À remplacer par une requête réelle
            'followed_companies' => 8, // À remplacer par une requête réelle
            'interviews_scheduled' => 2 // À remplacer par une requête réelle
        ];
        
        // Données des dernières offres d'emploi
        $recent_jobs = [
            [
                'id' => 1,
                'title' => 'Développeur Web Junior',
                'company_name' => 'Tech Solutions',
                'contract_type' => 'CDI',
                'created_at' => new \DateTime('2023-06-15')
            ],
            // Ajouter d'autres offres...
        ];
        
        // Données des candidatures récentes
        $recent_applications = [
            [
                'job_title' => 'Développeur Frontend',
                'company_name' => 'Digital Agency',
                'status' => 'En attente',
                'created_at' => new \DateTime('2023-06-10')
            ],
            // Ajouter d'autres candidatures...
        ];
        
        // Données des événements à venir
        $upcoming_events = [
            [
                'title' => 'Job Dating - Session Été',
                'date' => new \DateTime('2023-07-05 14:00'),
                'description' => 'Rencontre avec plusieurs entreprises du secteur technologique'
            ],
            // Ajouter d'autres événements...
        ];
        
        $data = [
            'title' => 'Dashboard Apprenant',
            'user' => [
                'name' => $this->session->get('user_name'),
                'email' => $this->session->get('user_email'),
                'role' => $this->session->get('user_role')
            ],
            'stats' => $stats,
            'recent_jobs' => $recent_jobs,
            'recent_applications' => $recent_applications,
            'upcoming_events' => $upcoming_events
        ];
        
        $this->render('front/dashboard/index', $data);
    }
}