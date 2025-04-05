<?php
/**
* Contrôleur pour l'authentification
*/
class AuthController extends Controller {
   /**
    * Affiche le formulaire de connexion
    */
   public function login() {
       // Rediriger si déjà connecté
       if (Auth::isLoggedIn()) {
           $this->redirect('');
           return;
       }
       
       $this->view('auth/login', [
           'title' => 'Connexion'
       ]);
   }
   
   /**
    * Traite la soumission du formulaire de connexion
    */
   public function authenticate() {
       // Vérifier si la requête est de type POST
       if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
           $this->redirect('auth/login');
           return;
       }
       
       // Vérifier si c'est une requête AJAX
       $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                 strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
       
       // Récupérer les données du formulaire
       $username = $_POST['username'] ?? '';
       $password = $_POST['password'] ?? '';
       
       // Valider les données
       $errors = [];
       
       if (empty($username)) {
           $errors['username'] = 'Le nom d\'utilisateur est requis';
       }
       
       if (empty($password)) {
           $errors['password'] = 'Le mot de passe est requis';
       }
       
       // S'il y a des erreurs
       if (!empty($errors)) {
           if ($isAjax) {
               // Répondre en JSON pour les requêtes AJAX
               header('Content-Type: application/json');
               echo json_encode([
                   'success' => false,
                   'errors' => $errors
               ]);
               exit;
           } else {
               // Afficher le formulaire avec les erreurs pour les requêtes normales
               $this->view('auth/login', [
                   'title' => 'Connexion',
                   'errors' => $errors,
                   'username' => $username
               ]);
               return;
           }
       }
       
       // Tenter d'authentifier l'utilisateur
       $userModel = new UserModel();
       $user = $userModel->authenticate($username, $password);
       
       if ($user) {
           // Connecter l'utilisateur
           Auth::login($user);
           
           if ($isAjax) {
               // Répondre en JSON pour les requêtes AJAX
               header('Content-Type: application/json');
               echo json_encode([
                   'success' => true,
                   'redirect' => APP_URL
               ]);
               exit;
           } else {
               // Rediriger vers la page d'accueil pour les requêtes normales
               $this->redirect('');
           }
       } else {
           // Authentification échouée
           if ($isAjax) {
               // Répondre en JSON pour les requêtes AJAX
               header('Content-Type: application/json');
               echo json_encode([
                   'success' => false,
                   'error' => 'Nom d\'utilisateur ou mot de passe incorrect'
               ]);
               exit;
           } else {
               // Afficher une erreur pour les requêtes normales
               $this->view('auth/login', [
                   'title' => 'Connexion',
                   'error' => 'Nom d\'utilisateur ou mot de passe incorrect',
                   'username' => $username
               ]);
           }
       }
   }
   
   /**
    * Affiche le formulaire d'inscription
    */
   public function register() {
       // Rediriger si déjà connecté
       if (Auth::isLoggedIn()) {
           $this->redirect('');
           return;
       }
       
       $this->view('auth/register', [
           'title' => 'Inscription'
       ]);
   }
   
   /**
    * Traite la soumission du formulaire d'inscription
    */
   public function store() {
       // Vérifier si la requête est de type POST
       if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
           $this->redirect('auth/register');
           return;
       }
       
       // Récupérer les données du formulaire
       $username = $_POST['username'] ?? '';
       $email = $_POST['email'] ?? '';
       $password = $_POST['password'] ?? '';
       $confirmPassword = $_POST['confirm_password'] ?? '';
       
       // Valider les données
       $errors = [];
       
       if (empty($username)) {
           $errors['username'] = 'Le nom d\'utilisateur est requis';
       }
       
       if (empty($email)) {
           $errors['email'] = 'L\'adresse e-mail est requise';
       } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
           $errors['email'] = 'L\'adresse e-mail n\'est pas valide';
       }
       
       if (empty($password)) {
           $errors['password'] = 'Le mot de passe est requis';
       } elseif (strlen($password) < 6) {
           $errors['password'] = 'Le mot de passe doit contenir au moins 6 caractères';
       }
       
       if ($password !== $confirmPassword) {
           $errors['confirm_password'] = 'Les mots de passe ne correspondent pas';
       }
       
       // Vérifier si le nom d'utilisateur ou l'e-mail existe déjà
       $userModel = new UserModel();
       
       if ($userModel->usernameExists($username)) {
           $errors['username'] = 'Ce nom d\'utilisateur est déjà utilisé';
       }
       
       if ($userModel->emailExists($email)) {
           $errors['email'] = 'Cette adresse e-mail est déjà utilisée';
       }
       
       // S'il y a des erreurs, afficher le formulaire avec les erreurs
       if (!empty($errors)) {
           $this->view('auth/register', [
               'title' => 'Inscription',
               'errors' => $errors,
               'username' => $username,
               'email' => $email
           ]);
           return;
       }
       
       // Créer l'utilisateur
       $userId = $userModel->create([
           'username' => $username,
           'mail' => $email,
           'password' => $password,
           'is_admin' => 0
       ]);
       
       if ($userId) {
           // Envoyer un email de confirmation
           EmailService::sendRegistrationConfirmation($email, $username);
           
           // Connecter l'utilisateur
           Auth::login([
               'id' => $userId,
               'username' => $username,
               'is_admin' => 0
           ]);
           
           // Ajouter un message de succès
           $_SESSION['success'] = 'Votre inscription a été effectuée avec succès. Un email de confirmation vous a été envoyé.';
           
           // Rediriger vers la page d'accueil
           $this->redirect('');
       } else {
           // Afficher une erreur
           $this->view('auth/register', [
               'title' => 'Inscription',
               'error' => 'Une erreur est survenue lors de l\'inscription',
               'username' => $username,
               'email' => $email
           ]);
       }
   }
   
   /**
    * Déconnecte l'utilisateur
    */
   public function logout() {
       Auth::logout();
       $this->redirect('');
   }
}

