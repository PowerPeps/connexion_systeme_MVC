<?php
/**
 * Classe UserModel - Gère les opérations liées aux utilisateurs
 */
class UserModel extends Model {
    protected $table = 'users';
    
    /**
     * Crée un nouvel utilisateur
     * 
     * @param array $data Données de l'utilisateur
     * @return int|false ID de l'utilisateur créé ou false
     */
    public function create($data) {
        // Hacher le mot de passe
        if (isset($data['password'])) {
            $data['password'] = Auth::hashPassword($data['password']);
        }
        
        return parent::create($data);
    }
    
    /**
     * Met à jour un utilisateur
     * 
     * @param int $id ID de l'utilisateur
     * @param array $data Données à mettre à jour
     * @return bool Succès ou échec
     */
    public function update($id, $data) {
        // Hacher le mot de passe si présent
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Auth::hashPassword($data['password']);
        } else {
            // Ne pas mettre à jour le mot de passe s'il est vide
            unset($data['password']);
        }
        
        return parent::update($id, $data);
    }
    
    /**
     * Authentifie un utilisateur
     * 
     * @param string $username Nom d'utilisateur
     * @param string $password Mot de passe
     * @return array|false Données de l'utilisateur ou false
     */
    public function authenticate($username, $password) {
        // Rechercher l'utilisateur par nom d'utilisateur
        $user = $this->findOneBy(['username' => $username]);
        
        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if ($user && Auth::verifyPassword($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Vérifie si un nom d'utilisateur existe déjà
     * 
     * @param string $username Nom d'utilisateur
     * @param int $excludeId ID à exclure (pour les mises à jour)
     * @return bool Existence du nom d'utilisateur
     */
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE username = :username";
        
        if ($excludeId) {
            $sql .= " AND id != :id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':username', $username);
        
        if ($excludeId) {
            $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Vérifie si une adresse e-mail existe déjà
     * 
     * @param string $email Adresse e-mail
     * @param int $excludeId ID à exclure (pour les mises à jour)
     * @return bool Existence de l'adresse e-mail
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE mail = :email";
        
        if ($excludeId) {
            $sql .= " AND id != :id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        
        if ($excludeId) {
            $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
}

