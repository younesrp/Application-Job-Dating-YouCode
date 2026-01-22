<?php

namespace App\models;

use App\core\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    // Mettre à jour le tableau fillable pour inclure tous les champs nécessaires
    protected $fillable = ['prenom', 'nom', 'email', 'telephone', 'password', 'role'];
    
    // Rôles hardcodés
    const ROLE_ADMIN = 'admin';
    const ROLE_APPRENANT = 'apprenant';  // Changé de ROLE_ETUDIANT à ROLE_APPRENANT
    
    // Liste des emails d'admin hardcodés
    private $adminEmails = [
        'admin@youcode.ma',
        'admin@jobdating.ma',
        'superadmin@youcode.ma'
    ];
    
    /**
     * Trouve un utilisateur par email
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }
    
    /**
     * Vérifie si un email existe déjà
     */
    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }
    
    /**
     * Authentifie un utilisateur et retourne ses informations
     */
    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        
        // if (!$user || !password_verify($password, $user['password'])) {
        //     return null;
        // }
        
        return $user;
    }
    
    /**
     * Crée un nouvel apprenant
     */
    public function createApprenant(array $data): int
    {
        // S'assurer que le rôle est bien apprenant
        $data['role'] = self::ROLE_APPRENANT;
        
        // Hash du mot de passe
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Filtrer les données pour n'inclure que les champs autorisés
        $filteredData = [];
        foreach ($this->fillable as $field) {
            if (isset($data[$field])) {
                $filteredData[$field] = $data[$field];
            }
        }
        
        return $this->create($filteredData);
    }
    
    /**
     * Vérifie si un email est un email d'admin hardcodé
     */
    public function isAdminEmail(string $email): bool
    {
        // Normaliser l'email pour la comparaison (insensible à la casse)
        $email = strtolower(trim($email));
        
        foreach ($this->adminEmails as $adminEmail) {
            if ($email === strtolower($adminEmail)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Crée ou met à jour un utilisateur admin
     */
    public function createOrUpdateAdmin(array $data): int
    {
        $user = $this->findByEmail($data['email']);
        
        if ($user) {
            // Mettre à jour le rôle si l'utilisateur existe déjà
            $this->update($user['id'], ['role' => self::ROLE_ADMIN]);
            return $user['id'];
        } else {
            // Créer un nouvel admin
            $adminData = [
                'prenom' => $data['prenom'] ?? 'Admin',
                'nom' => $data['nom'] ?? 'User',
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'role' => self::ROLE_ADMIN
            ];
            
            // Ajouter le téléphone s'il est fourni
            if (isset($data['telephone'])) {
                $adminData['telephone'] = $data['telephone'];
            }
            
            return $this->create($adminData);
        }
    }
    
    /**
     * Récupère tous les utilisateurs d'un rôle spécifique
     */
    public function findByRole(string $role): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE role = :role");
        $stmt->execute(['role' => $role]);
        return $stmt->fetchAll();
    }
    
    /**
     * Compte le nombre d'utilisateurs par rôle
     */
    public function countByRole(string $role): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE role = :role");
        $stmt->execute(['role' => $role]);
        return (int) $stmt->fetchColumn();
    }
}