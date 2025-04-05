<?php
/**
* Contrôleur pour la page d'accueil
*/
class HomeController extends Controller {
   /**
    * Action par défaut - Affiche la page d'accueil
    */
   public function index() {
       $this->view('home/index', [
           'title' => APP_NAME . ' - Accueil',
           'user' => Auth::getUser()
       ]);
   }
}

