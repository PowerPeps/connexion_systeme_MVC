<?php
/**
* Classe Controller - Classe de base pour tous les contrôleurs
*/
class Controller {
   /**
    * Charge et affiche une vue
    * 
    * @param string $view Nom de la vue à charger
    * @param array $data Données à passer à la vue
    * @param bool $includeLayout Inclure ou non le layout
    */
   protected function view($view, $data = [], $includeLayout = true) {
       // Extraire les données pour les rendre disponibles dans la vue
       extract($data);
       
       // Chemin vers le fichier de vue
       $viewFile = ROOT_PATH . '/views/' . $view . '.php';
       
       // Vérifier si le fichier de vue existe
       if (!file_exists($viewFile)) {
           die("Vue non trouvée: $viewFile");
       }
       
       // Démarrer la mise en tampon de sortie
       ob_start();
       
       // Inclure le header si nécessaire
       if ($includeLayout) {
           include ROOT_PATH . '/views/layouts/header.php';
       }
       
       // Inclure la vue
       include $viewFile;
       
       // Inclure le footer si nécessaire
       if ($includeLayout) {
           include ROOT_PATH . '/views/layouts/footer.php';
       }
       
       // Récupérer le contenu du tampon et l'afficher
       echo ob_get_clean();
   }
   
   /**
    * Redirige vers une URL spécifique
    * 
    * @param string $url URL de redirection
    */
   protected function redirect($url) {
       header('Location: ' . APP_URL . '/' . $url);
       exit;
   }
   
   /**
    * Méthode par défaut pour les pages non trouvées
    */
   public function notFound() {
       $this->view('error/404', ['title' => 'Page non trouvée']);
   }
}

