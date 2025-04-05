<?php
/**
* Contrôleur pour la gestion des erreurs
*/
class ErrorController extends Controller {
   /**
    * Affiche la page 404 (non trouvée)
    */
   public function notFound() {
       http_response_code(404);
       $this->view('error/404', [
           'title' => 'Page non trouvée'
       ]);
   }
   
   /**
    * Affiche la page 403 (accès refusé)
    */
   public function forbidden() {
       http_response_code(403);
       $this->view('error/403', [
           'title' => 'Accès refusé'
       ]);
   }
   
   /**
    * Affiche la page 500 (erreur serveur)
    */
   public function serverError() {
       http_response_code(500);
       $this->view('error/500', [
           'title' => 'Erreur serveur'
       ]);
   }
}

