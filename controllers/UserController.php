<?php
/**
 * Contrôleur pour la gestion des utilisateurs
 */
class UserController extends Controller {
    /**
     * Constructeur - Vérifie les autorisations
     */
    public function __construct() {
        // Vérifier si l'utilisateur est connecté et est administrateur
        if (!Auth::isLoggedIn() || !Auth::isAdmin()) {
            // Rediriger vers la page d'accueil avec un message d'erreur
            $_SESSION['error'] = 'Accès non autorisé';
            $this->redirect('');
            exit;
        }
    }
    
    /**
     * Affiche la liste des utilisateurs
     */
    public function index() {
        $userModel = new UserModel();
        $users = $userModel->getAll('username');
        
        $this->view('users/index', [
            'title' => 'Gestion des utilisateurs',
            'users' => $users
        ]);
    }
    
    /**
     * Affiche le formulaire de création d'utilisateur
     */
    public function create() {
        $this->view('users/create', [
            'title' => 'Créer un utilisateur'
        ]);
    }
    
    /**
     * Traite la soumission du formulaire de création d'utilisateur
     */
    public function store() {
        // Vérifier si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user/create');
            return;
        }
        
        // Récupérer les données du formulaire
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $isAdmin = isset($_POST['is_admin']) ? 1 : 0;
        
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
            $this->view('users/create', [
                'title' => 'Créer un utilisateur',
                'errors' => $errors,
                'username' => $username,
                'email' => $email,
                'is_admin' => $isAdmin
            ]);
            return;
        }
        
        // Créer l'utilisateur
        $userId = $userModel->create([
            'username' => $username,
            'mail' => $email,
            'password' => $password,
            'is_admin' => $isAdmin
        ]);
        
        if ($userId) {
            // Rediriger vers la liste des utilisateurs avec un message de succès
            $_SESSION['success'] = 'Utilisateur créé avec succès';
            $this->redirect('user');
        } else {
            // Afficher une erreur
            $this->view('users/create', [
                'title' => 'Créer un utilisateur',
                'error' => 'Une erreur est survenue lors de la création de l\'utilisateur',
                'username' => $username,
                'email' => $email,
                'is_admin' => $isAdmin
            ]);
        }
    }
    
    /**
     * Affiche le formulaire de modification d'utilisateur
     * 
     * @param int $id ID de l'utilisateur
     */
    public function edit($id) {
        $userModel = new UserModel();
        $user = $userModel->getById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur non trouvé';
            $this->redirect('user');
            return;
        }
        
        $this->view('users/edit', [
            'title' => 'Modifier un utilisateur',
            'user' => $user
        ]);
    }
    
    /**
     * Traite la soumission du formulaire de modification d'utilisateur
     * 
     * @param int $id ID de l'utilisateur
     */
    public function update($id) {
        // Vérifier si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('user/edit/' . $id);
            return;
        }
        
        $userModel = new UserModel();
        $user = $userModel->getById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur non trouvé';
            $this->redirect('user');
            return;
        }
        
        // Récupérer les données du formulaire
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $isAdmin = isset($_POST['is_admin']) ? 1 : 0;
        
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
        
        if (!empty($password) && strlen($password) < 6) {
            $errors['password'] = 'Le mot de passe doit contenir au moins 6 caractères';
        }
        
        // Vérifier si le nom d'utilisateur ou l'e-mail existe déjà (en excluant l'utilisateur actuel)
        if ($userModel->usernameExists($username, $id)) {
            $errors['username'] = 'Ce nom d\'utilisateur est déjà utilisé';
        }
        
        if ($userModel->emailExists($email, $id)) {
            $errors['email'] = 'Cette adresse e-mail est déjà utilisée';
        }
        
        // S'il y a des erreurs, afficher le formulaire avec les erreurs
        if (!empty($errors)) {
            $this->view('users/edit', [
                'title' => 'Modifier un utilisateur',
                'errors' => $errors,
                'user' => array_merge($user, [
                    'username' => $username,
                    'mail' => $email,
                    'is_admin' => $isAdmin
                ])
            ]);
            return;
        }
        
        // Préparer les données à mettre à jour
        $data = [
            'username' => $username,
            'mail' => $email,
            'is_admin' => $isAdmin
        ];
        
        // Ajouter le mot de passe s'il est fourni
        if (!empty($password)) {
            $data['password'] = $password;
        }
        
        // Mettre à jour l'utilisateur
        $success = $userModel->update($id, $data);
        
        if ($success) {
            // Rediriger vers la liste des utilisateurs avec un message de succès
            $_SESSION['success'] = 'Utilisateur mis à jour avec succès';
            $this->redirect('user');
        } else {
            // Afficher une erreur
            $this->view('users/edit', [
                'title' => 'Modifier un utilisateur',
                'error' => 'Une erreur est survenue lors de la mise à jour de l\'utilisateur',
                'user' => array_merge($user, [
                    'username' => $username,
                    'mail' => $email,
                    'is_admin' => $isAdmin
                ])
            ]);
        }
    }
    
    /**
     * Supprime un utilisateur
     * 
     * @param int $id ID de l'utilisateur
     */
    public function delete($id) {
        $userModel = new UserModel();
        $user = $userModel->getById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur non trouvé';
            $this->redirect('user');
            return;
        }
        
        // Empêcher la suppression de son propre compte
        if ($id == Auth::getUserId()) {
            $_SESSION['error'] = 'Vous ne pouvez pas supprimer votre propre compte';
            $this->redirect('user');
            return;
        }
        
        // Supprimer l'utilisateur
        $success = $userModel->delete($id);
        
        if ($success) {
            $_SESSION['success'] = 'Utilisateur supprimé avec succès';
        } else {
            $_SESSION['error'] = 'Une erreur est survenue lors de la suppression de l\'utilisateur';
        }
        
        $this->redirect('user');
    }
}

