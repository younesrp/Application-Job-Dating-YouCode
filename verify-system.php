#!/usr/bin/env php
<?php
/**
 * Script de Vérification du Système de Candidature
 * Usage: php verify-system.php
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║    Vérification du Système de Candidature avec Upload CV       ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Vérifier les fichiers PHP
echo "📋 Vérification des fichiers PHP...\n";
$phpFiles = [
    'app/controllers/front/CandidatureController.php' => 'Contrôleur Candidature',
    'app/models/Candidature.php' => 'Modèle Candidature',
    'app/controllers/front/DashboardController.php' => 'Contrôleur Dashboard',
    'app/core/Session.php' => 'Core Session',
];

foreach ($phpFiles as $file => $desc) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $success[] = "✅ $desc: OK";
    } else {
        $errors[] = "❌ $desc: MANQUANT ($file)";
    }
}

// 2. Vérifier les fichiers Twig
echo "\n📋 Vérification des templates Twig...\n";
$twigFiles = [
    'app/views/front/dashboard/modal-candidature.twig' => 'Modal Candidature',
    'app/views/components/flash-messages.twig' => 'Flash Messages',
    'app/views/front/dashboard/index.twig' => 'Dashboard',
    'app/views/front/layout.twig' => 'Layout',
];

foreach ($twigFiles as $file => $desc) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        $success[] = "✅ $desc: OK";
    } else {
        $errors[] = "❌ $desc: MANQUANT ($file)";
    }
}

// 3. Vérifier les répertoires
echo "\n📁 Vérification des répertoires...\n";
$directories = [
    'public/uploads/cvs' => 'Uploads CVs',
];

foreach ($directories as $dir => $desc) {
    $path = __DIR__ . '/' . $dir;
    if (is_dir($path)) {
        $success[] = "✅ $desc: EXISTS ($path)";
        if (is_writable($path)) {
            $success[] = "   ✓ Répertoire accessible en écriture";
        } else {
            $warnings[] = "⚠️  $desc: NON accessible en écriture";
        }
    } else {
        $errors[] = "❌ $desc: MANQUANT ($dir)";
    }
}

// 4. Vérifier la base de données
echo "\n📊 Vérification de la base de données...\n";
try {
    $conn = new PDO('mysql:host=localhost', 'root', '');
    $conn->exec('USE job_dating_youcode');
    
    // Vérifier la colonne cv_path
    $result = $conn->query("SHOW COLUMNS FROM candidatures LIKE 'cv_path'");
    if ($result->rowCount() > 0) {
        $success[] = "✅ Colonne cv_path: EXISTS";
    } else {
        $errors[] = "❌ Colonne cv_path: MANQUANTE (ALTER TABLE requise)";
    }
    
    // Vérifier la table candidatures
    $result = $conn->query("SHOW TABLES LIKE 'candidatures'");
    if ($result->rowCount() > 0) {
        $success[] = "✅ Table candidatures: EXISTS";
    } else {
        $errors[] = "❌ Table candidatures: MANQUANTE";
    }
} catch (PDOException $e) {
    $errors[] = "❌ BD: Erreur de connexion - " . $e->getMessage();
}

// 5. Vérifier les routes
echo "\n🛣️  Vérification des routes...\n";
$routePath = __DIR__ . '/public/index.php';
if (file_exists($routePath)) {
    $content = file_get_contents($routePath);
    
    if (strpos($content, '/candidature') !== false) {
        $success[] = "✅ Route POST /candidature: CONFIGURÉE";
    } else {
        $errors[] = "❌ Route POST /candidature: NON TROUVÉE";
    }
    
    if (strpos($content, 'CandidatureController') !== false) {
        $success[] = "✅ CandidatureController importé: OK";
    } else {
        $errors[] = "❌ CandidatureController: NON IMPORTÉ";
    }
} else {
    $errors[] = "❌ public/index.php: MANQUANT";
}

// 6. Vérifier les constantes de sécurité
echo "\n🔐 Vérification de la sécurité...\n";
$controllerPath = __DIR__ . '/app/controllers/front/CandidatureController.php';
if (file_exists($controllerPath)) {
    $content = file_get_contents($controllerPath);
    
    $checks = [
        'MAX_FILE_SIZE' => 'Limite de taille définie',
        'uploadCV' => 'Méthode uploadCV exists',
        'validateCandidatureData' => 'Méthode validation exists',
        'csrf_token' => 'Vérification CSRF',
        'application/pdf' => 'Vérification MIME type',
    ];
    
    foreach ($checks as $check => $desc) {
        if (strpos($content, $check) !== false) {
            $success[] = "✅ $desc: IMPLÉMENTÉ";
        } else {
            $warnings[] = "⚠️  $desc: NON TROUVÉ";
        }
    }
} else {
    $errors[] = "❌ CandidatureController: MANQUANT";
}

// 7. Afficher les résultats
echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                        RÉSULTATS                               ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n";
echo "\n";

if (!empty($success)) {
    foreach ($success as $msg) {
        echo $msg . "\n";
    }
}

if (!empty($warnings)) {
    echo "\n⚠️  AVERTISSEMENTS:\n";
    foreach ($warnings as $msg) {
        echo $msg . "\n";
    }
}

if (!empty($errors)) {
    echo "\n❌ ERREURS:\n";
    foreach ($errors as $msg) {
        echo $msg . "\n";
    }
}

// Statut final
echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";

if (empty($errors)) {
    echo "║               ✅ SYSTÈME VÉRIFIÉ AVEC SUCCÈS                 ║\n";
} else {
    echo "║               ❌ ERREURS DÉTECTÉES (" . count($errors) . ")                    ║\n";
}

echo "╚════════════════════════════════════════════════════════════════╝\n";

// Stats
echo "\n📊 Statistiques:\n";
echo "   ✅ Succès: " . count($success) . "\n";
echo "   ⚠️  Avertissements: " . count($warnings) . "\n";
echo "   ❌ Erreurs: " . count($errors) . "\n";

echo "\n";

// Code de sortie
exit(empty($errors) ? 0 : 1);
