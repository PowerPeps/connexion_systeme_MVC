<?php
/**
 * Classe TemplateModel - Modèle pour créer de nouveaux modèles
 */
class TemplateModel extends Model {
    // Définir le nom de la table (optionnel, sinon déduit du nom de la classe)
    protected $table = 'templates';
    
    /**
     * Constructeur - Initialise la connexion à la base de données
     */
    public function __construct() {
        parent::__construct();
        
        // Créer la table si elle n'existe pas (exemple)
        $this->createTableIfNotExists();
    }
    
    /**
     * Crée la table si elle n'existe pas
     */
    private function createTableIfNotExists() {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `description` text,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        try {
            $this->db->exec($sql);
        } catch (PDOException $e) {
            // Gérer l'erreur (log, etc.)
        }
    }
    
    /**
     * Méthode personnalisée pour rechercher par nom
     * 
     * @param string $name Nom à rechercher
     * @return array Résultats de la recherche
     */
    public function findByName($name) {
        $sql = "SELECT * FROM {$this->table} WHERE name LIKE :name";
        $stmt = $this->db->prepare($sql);
        $searchTerm = "%{$name}%";
        $stmt->bindParam(':name', $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Méthode personnalisée pour récupérer les éléments récents
     * 
     * @param int $limit Nombre d'éléments à récupérer
     * @return array Éléments récents
     */
    public function getRecent($limit = 5) {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Méthode personnalisée pour compter les éléments
     * 
     * @return int Nombre d'éléments
     */
    public function count() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        return $this->db->query($sql)->fetchColumn();
    }
}

