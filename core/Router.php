<?php
/**
 * Classe Router - Gère le routage des requêtes HTTP
 */
class Router {
    /**
     * Tableau des routes définies
     * @var array
     */
    private $routes = [];
    
    /**
     * Contrôleur par défaut pour les erreurs 404
     * @var string
     */
    private $notFoundController = 'ErrorController';
    
    /**
     * Méthode par défaut pour les erreurs 404
     * @var string
     */
    private $notFoundAction = 'notFound';
    
    /**
     * Ajoute une route au routeur
     * 
     * @param string $method Méthode HTTP (GET, POST, etc.)
     * @param string $path Chemin de la route
     * @param string $handler Contrôleur et méthode (format: 'ControllerName@methodName')
     * @return Router Instance du routeur (pour le chaînage)
     */
    public function addRoute($method, $path, $handler) {
        // Normaliser le chemin
        $path = trim($path, '/');
        
        // Ajouter la route au tableau des routes
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
        
        return $this;
    }
    
    /**
     * Définit le contrôleur et la méthode pour les erreurs 404
     * 
     * @param string $handler Contrôleur et méthode (format: 'ControllerName@methodName')
     * @return Router Instance du routeur (pour le chaînage)
     */
    public function setNotFoundHandler($handler) {
        list($this->notFoundController, $this->notFoundAction) = explode('@', $handler);
        return $this;
    }
    
    /**
     * Dispatch la requête vers le contrôleur et l'action appropriés
     */
    public function dispatch() {
        // Récupérer la méthode HTTP
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Récupérer le chemin de la requête
        $requestUri = $_SERVER['REQUEST_URI'];
        $basePath = ROOT_PATH_DIFF.'/';
        $requestPath = '';
        
        if (strpos($requestUri, $basePath) === 0) {
            $requestPath = substr($requestUri, strlen($basePath));
        }
        
        // Supprimer les paramètres de requête s'il y en a
        if (($pos = strpos($requestPath, '?')) !== false) {
            $requestPath = substr($requestPath, 0, $pos);
        }
        
        // Normaliser le chemin
        $requestPath = trim($requestPath, '/');
        
        // Chercher une route correspondante
        $matchedRoute = null;
        $params = [];
        
        foreach ($this->routes as $route) {
            // Vérifier si la méthode HTTP correspond
            if ($route['method'] !== $requestMethod && $route['method'] !== 'ANY') {
                continue;
            }
            
            // Convertir le chemin de la route en expression régulière
            $pattern = $this->convertRouteToRegex($route['path']);
            
            // Vérifier si le chemin correspond
            if (preg_match($pattern, $requestPath, $matches)) {
                $matchedRoute = $route;
                
                // Extraire les paramètres
                array_shift($matches); // Supprimer la correspondance complète
                $params = $matches;
                
                break;
            }
        }
        
        // Si aucune route ne correspond, utiliser le gestionnaire 404
        if ($matchedRoute === null) {
            $controller = $this->notFoundController;
            $action = $this->notFoundAction;
        } else {
            // Extraire le contrôleur et l'action
            list($controller, $action) = explode('@', $matchedRoute['handler']);
        }
        
        // Ajouter "Controller" au nom du contrôleur s'il n'est pas déjà présent
        if (strpos($controller, 'Controller') === false) {
            $controller .= 'Controller';
        }
        
        // Vérifier si le contrôleur existe
        $controllerFile = ROOT_PATH . '/controllers/' . $controller . '.php';
        
        if (!file_exists($controllerFile)) {
            // Utiliser le gestionnaire 404 si le contrôleur n'existe pas
            $controller = $this->notFoundController;
            $action = $this->notFoundAction;
        }
        
        // Instancier le contrôleur
        $controllerInstance = new $controller();
        
        // Vérifier si l'action existe
        if (!method_exists($controllerInstance, $action)) {
            // Utiliser le gestionnaire 404 si l'action n'existe pas
            $controller = $this->notFoundController;
            $action = $this->notFoundAction;
            $controllerInstance = new $controller();
        }
        
        // Appeler l'action avec les paramètres
        call_user_func_array([$controllerInstance, $action], $params);
    }
    
    /**
     * Convertit un chemin de route en expression régulière
     * 
     * @param string $route Chemin de la route
     * @return string Expression régulière
     */
    private function convertRouteToRegex($route) {
        // Remplacer les paramètres {param} par des expressions régulières
        $route = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);
        
        // Ajouter les délimiteurs et les ancres
        return '/^' . str_replace('/', '\/', $route) . '$/';
    }
}

