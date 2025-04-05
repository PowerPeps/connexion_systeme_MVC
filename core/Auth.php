<?php
/**
 * Classe Auth - Gère l'authentification et les autorisations
 */
class Auth {
    /**
     * Vérifie si un utilisateur est connecté
     * 
     * @return bool État de connexion
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Vérifie si l'utilisateur connecté est un administrateur
     * 
     * @return bool État d'administrateur
     */
    public static function isAdmin() {
        return self::isLoggedIn() && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }
    
    /**
     * Récupère l'ID de l'utilisateur connecté
     * 
     * @return int|null ID de l'utilisateur ou null
     */
    public static function getUserId() {
        return self::isLoggedIn() ? $_SESSION['user_id'] : null;
    }
    
    /**
     * Récupère les informations de l'utilisateur connecté
     * 
     * @return array|null Informations de l'utilisateur ou null
     */
    public static function getUser() {
        if (self::isLoggedIn()) {
            $userModel = new UserModel();
            return $userModel->getById($_SESSION['user_id']);
        }
        
        return null;
    }
    
    /**
     * Connecte un utilisateur
     * 
     * @param array $user Données de l'utilisateur
     */
    public static function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        // Régénérer l'ID de session pour éviter les attaques de fixation de session
        session_regenerate_id(true);
    }
    
    /**
     * Déconnecte l'utilisateur
     */
    public static function logout() {
        // Détruire toutes les données de session
        $_SESSION = [];
        
        // Détruire le cookie de session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Détruire la session
        session_destroy();
    }
    
    /**
     * Vérifie si l'utilisateur a accès à une ressource
     * 
     * @param string $resource Ressource à vérifier
     * @return bool Autorisation
     */
    public static function checkAccess($resource) {
        // Ressources accessibles sans connexion
        $publicResources = [
            'HomeController@index',
            'AuthController@login',
            'AuthController@register'
        ];
        
        // Ressources accessibles uniquement aux administrateurs
        $adminResources = [
            'UserController@index',
            'UserController@create',
            'UserController@edit',
            'UserController@delete'
        ];
        
        // Vérifier si la ressource est publique
        if (in_array($resource, $publicResources)) {
            return true;
        }
        
        // Vérifier si l'utilisateur est connecté
        if (!self::isLoggedIn()) {
            return false;
        }
        
        // Vérifier si la ressource nécessite des droits d'administrateur
        if (in_array($resource, $adminResources) && !self::isAdmin()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Hache un mot de passe
     * 
     * @param string $password Mot de passe en clair
     * @return string Mot de passe haché
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
    }
    
    /**
     * Vérifie un mot de passe
     * 
     * @param string $password Mot de passe en clair
     * @param string $hash Hash stocké
     * @return bool Validité du mot de passe
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}

