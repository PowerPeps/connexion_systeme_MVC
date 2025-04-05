<?php
/**
 * Classe Model - Classe de base pour tous les modèles
 */
class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    /**
     * Constructeur - Initialise la connexion à la base de données
     */
    public function __construct() {
        $this->db = Database::getInstance();
        
        // Si le nom de la table n'est pas défini, le déduire du nom de la classe
        if (empty($this->table)) {
            // Récupérer le nom de la classe sans le suffixe "Model"
            $className = get_class($this);
            $className = str_replace('Model', '', $className);
            
            // Convertir en pluriel et en minuscules
            $this->table = strtolower($className) . 's';
        }
    }
    
    /**
     * Récupère tous les enregistrements de la table
     * 
     * @param string $orderBy Champ pour le tri
     * @param string $order Direction du tri (ASC ou DESC)
     * @return array Tableau d'enregistrements
     */
    public function getAll($orderBy = null, $order = 'ASC') {
        $sql = "SELECT * FROM {$this->table}";
        
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy} {$order}";
        }
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère un enregistrement par son ID
     * 
     * @param int $id ID de l'enregistrement
     * @return array|false Enregistrement trouvé ou false
     */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crée un nouvel enregistrement
     * 
     * @param array $data Données à insérer
     * @return int|false ID de l'enregistrement créé ou false
     */
    public function create($data) {
        // Préparer les colonnes et les valeurs
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        // Lier les paramètres
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        // Exécuter la requête
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Met à jour un enregistrement existant
     * 
     * @param int $id ID de l'enregistrement
     * @param array $data Données à mettre à jour
     * @return bool Succès ou échec
     */
    public function update($id, $data) {
        // Préparer les paires colonne=valeur
        $setPart = '';
        foreach ($data as $key => $value) {
            $setPart .= "{$key} = :{$key}, ";
        }
        $setPart = rtrim($setPart, ', ');
        
        $sql = "UPDATE {$this->table} SET {$setPart} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        
        // Lier les paramètres
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        // Exécuter la requête
        return $stmt->execute();
    }
    
    /**
     * Supprime un enregistrement
     * 
     * @param int $id ID de l'enregistrement
     * @return bool Succès ou échec
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Recherche des enregistrements selon des critères
     * 
     * @param array $criteria Critères de recherche (colonne => valeur)
     * @param string $operator Opérateur logique (AND ou OR)
     * @return array Enregistrements trouvés
     */
    public function findBy($criteria, $operator = 'AND') {
        // Construire la clause WHERE
        $wherePart = '';
        foreach ($criteria as $key => $value) {
            $wherePart .= "{$key} = :{$key} {$operator} ";
        }
        $wherePart = rtrim($wherePart, " {$operator} ");
        
        $sql = "SELECT * FROM {$this->table} WHERE {$wherePart}";
        $stmt = $this->db->prepare($sql);
        
        // Lier les paramètres
        foreach ($criteria as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Trouve un enregistrement unique selon des critères
     * 
     * @param array $criteria Critères de recherche
     * @return array|false Enregistrement trouvé ou false
     */
    public function findOneBy($criteria) {
        $results = $this->findBy($criteria);
        return !empty($results) ? $results[0] : false;
    }
}

