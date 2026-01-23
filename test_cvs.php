<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('.');
$dotenv->load();

try {
    $pdo = new PDO(
        'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS'] ?? ''
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query('SELECT id, cv_path FROM candidatures WHERE cv_path IS NOT NULL');
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Candidatures avec CV:\n";
    foreach ($results as $row) {
        echo 'ID: ' . $row['id'] . ' | CV: ' . $row['cv_path'] . PHP_EOL;
        $fullPath = $_SERVER['DOCUMENT_ROOT'] . $row['cv_path'];
        echo '   Full path: ' . $fullPath . PHP_EOL;
        echo '   Exists: ' . (file_exists($fullPath) ? 'OUI' : 'NON') . PHP_EOL;
    }
} catch (\Exception $e) {
    echo 'Erreur: ' . $e->getMessage();
}
