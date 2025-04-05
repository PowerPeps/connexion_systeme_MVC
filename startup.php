<?php


$baseDir = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__) . '/';
var_dump($baseDir);

require_once("config/config.php");


// Contenu du fichier .htaccess
$htaccessContent = <<<EOT
RewriteEngine On
RewriteBase {$baseDir}

# Ne pas appliquer les règles aux fichiers existants
RewriteCond %{REQUEST_FILENAME} -f
RewriteRule ^ - [L]

# Ne pas appliquer les règles aux dossiers existants
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

# Ne pas appliquer les règles aux demandes dans /media
RewriteCond %{REQUEST_URI} ^{$baseDir}media
RewriteRule ^ - [L]

# Rediriger toutes les autres requêtes vers index.php
RewriteRule ^(.*)$ index.php [QSA,L]

EOT;


// Écriture dans le fichier .htaccess
file_put_contents('.htaccess', $htaccessContent);

$htaccessFile = '.htaccess';

if (!file_exists($htaccessFile)) {
    if (file_put_contents($htaccessFile, $htaccessContent) !== false) {
        echo "Fichier .htaccess créé avec succès avec RewriteBase: " . $baseDir;
    } else {
        echo "Erreur lors de la création du fichier .htaccess";
    }
} else {
    if (file_put_contents($htaccessFile, $htaccessContent) !== false) {
        echo "Fichier .htaccess généré avec RewriteBase: " . $baseDir;
    } else {
        echo "Erreur lors de la mise à jour du fichier .htaccess";
    }
}



// Connexion au serveur MySQL
$sqlFile = 'default_db.sql';
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($mysqli->connect_error) {
    die("Échec de connexion : " . $mysqli->connect_error);
}

$result = $mysqli->query("SHOW DATABASES LIKE '" . DB_NAME . "'");

if ($result && $result->num_rows > 0) {
    echo "La db existe";
} else {
    $mysqli->query("CREATE DATABASE IF NOT EXISTS `".DB_NAME."`");
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        if ($mysqli->multi_query($sql)) {
            echo "Base de données initialisée.";
        } else {
            echo "Erreur lors de l'initialisation : " . $mysqli->error;
        }
    }
}
$mysqli->close();
?>
