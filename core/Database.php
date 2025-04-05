<?php
/**
* Classe Database - Gère la connexion à la base de données (Singleton)
*/
class Database {
   private static $instance = null;
   private $connection;
   
   /**
    * Constructeur privé - Établit la connexion à la base de données
    */
   private function __construct() {
       try {
           $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
           $options = [
               PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
               PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
               PDO::ATTR_EMULATE_PREPARES => false,
               // Ajouter un timeout plus long pour les connexions distantes
               PDO::ATTR_TIMEOUT => 5
           ];
           
           $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
       } catch (PDOException $e) {
           // Afficher un message d'erreur plus détaillé pour le débogage
           die("Erreur de connexion à la base de données: " . $e->getMessage() . 
               "<br>Host: " . DB_HOST . 
               "<br>Database: " . DB_NAME);
       }
   }
   
   /**
    * Empêche le clonage de l'instance
    */
   private function __clone() {}
   
   /**
    * Récupère l'instance unique de la base de données
    * 
    * @return PDO Instance de PDO
    */
   public static function getInstance() {
       if (self::$instance === null) {
           self::$instance = new self();
       }
       
       return self::$instance->connection;
   }
}

