<?php
/**
* Configuration de l'application
*/

$rootPath = defined('ROOT_PATH_DIFF') ? ROOT_PATH_DIFF : $baseDir ;

// Configuration de la base de données
define('DB_HOST', 'localhost:3306');
define('DB_NAME', $rootPath .'_DB');
define('DB_USER', 'root');
define('DB_PASS', 'YourPassword');

// Configuration de l'application
define('APP_NAME', 'Cours Interfaces');
define('DEFAULT_CONTROLLER', 'Home');
define('DEFAULT_ACTION', 'index');

define('APP_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]".$rootPath);

// Configuration de la sécurité
define('SESSION_LIFETIME', 3600); // 1 heure
define('HASH_COST', 10); // Coût du hachage bcrypt

define('DEBUG', false);