<?php
/**
 * Point d'entrée principal de l'application
 * 
 * Ce fichier initialise le framework et traite toutes les requêtes
 */

// Définition des constantes de base
define('ROOT_PATH', __DIR__);
define('ROOT_PATH_DIFF',str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__));

// Chargement des configurations
require_once ROOT_PATH . '/config/config.php';

// Afficher les erreurs pendant le développement
DEBUG ?ini_set('display_errors', 1):'';
DEBUG ?ini_set('display_startup_errors', 1):'';
DEBUG ?error_reporting(E_ALL):'';

// Chargement automatique des classes
spl_autoload_register(function ($class) {
    // Conversion du nom de classe en chemin de fichier
    $class = str_replace('\\', '/', $class);
    
    // Recherche dans les dossiers principaux
    $directories = ['core', 'models', 'controllers'];
    
    foreach ($directories as $directory) {
        $file = ROOT_PATH . '/' . $directory . '/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Initialisation de la session
session_start();

// Vérifier si c'est un fichier PHP direct
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = basename($_SERVER['SCRIPT_FILENAME']);

DEBUG ?var_dump($_SERVER['REQUEST_URI']):'';

if ($scriptName !== 'index.php' && preg_match('/\.php$/', $scriptName)) {
    // C'est un fichier PHP direct, ne pas router
    DEBUG ?var_dump("Accès direct détecté pour un fichier PHP. Aucun routage effectué."):'';
    return;
}

// Charger les routes
require_once ROOT_PATH . '/routes.php';

// Dispatcher les routes
$router->dispatch();

DEBUG ?var_dump(get_defined_constants()):'';

