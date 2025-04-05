<?php
/**
 * Définition des routes de l'application
 */

// Initialiser le routeur
$router = new Router();

// Définir le gestionnaire pour les erreurs 404
$router->setNotFoundHandler('Error@notFound');

// Routes pour la page d'accueil
$router->addRoute('GET', '/', 'Home@index');

// Routes pour l'authentification
$router->addRoute('GET', '/auth/login', 'Auth@login');
$router->addRoute('POST', '/auth/authenticate', 'Auth@authenticate');
$router->addRoute('GET', '/auth/register', 'Auth@register');
$router->addRoute('POST', '/auth/store', 'Auth@store');
$router->addRoute('GET', '/auth/logout', 'Auth@logout');

// Routes pour la gestion des utilisateurs
$router->addRoute('GET', '/user', 'User@index');
$router->addRoute('GET', '/user/create', 'User@create');
$router->addRoute('POST', '/user/store', 'User@store');
$router->addRoute('GET', '/user/edit/{id}', 'User@edit');
$router->addRoute('POST', '/user/update/{id}', 'User@update');
$router->addRoute('GET', '/user/delete/{id}', 'User@delete');

// Routes pour le template (exemple)
$router->addRoute('GET', '/template', 'Template@index');
$router->addRoute('GET', '/template/create', 'Template@create');
$router->addRoute('POST', '/template/store', 'Template@store');
$router->addRoute('GET', '/template/edit/{id}', 'Template@edit');
$router->addRoute('POST', '/template/update/{id}', 'Template@update');
$router->addRoute('GET', '/template/delete/{id}', 'Template@delete');

