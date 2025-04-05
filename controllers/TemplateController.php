<?php
/**
 * Contrôleur Template - Modèle pour créer de nouveaux contrôleurs
 */
class TemplateController extends Controller {
    /**
     * Constructeur - Initialise le contrôleur
     */
    public function __construct() {
        // Vérifier si l'utilisateur est connecté (si nécessaire)
        if (!Auth::isLoggedIn()) {
            // Rediriger vers la page de connexion
            $_SESSION['error'] = 'Vous devez être connecté pour accéder à cette page';
            $this->redirect('auth/login');
            exit;
        }
    }
    
    /**
     * Action par défaut - Affiche la page principale
     */
    public function index() {
        // Initialiser le modèle (si nécessaire)
        $templateModel = new TemplateModel();
        
        // Récupérer les données (exemple)
        $items = $templateModel->getAll();
        
        // Afficher la vue
        $this->view('template/index', [
            'title' => 'Titre de la page',
            'items' => $items
        ]);
    }
    
    /**
     * Affiche le formulaire de création
     */
    public function create() {
        $this->view('template/create', [
            'title' => 'Créer un nouvel élément'
        ]);
    }
    
    /**
     * Traite la soumission du formulaire de création
     */
    public function store() {
        // Vérifier si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('template/create');
            return;
        }
        
        // Récupérer les données du formulaire
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        
        // Valider les données
        $errors = [];
        
        if (empty($name)) {
            $errors['name'] = 'Le nom est requis';
        }
        
        // S'il y a des erreurs, afficher le formulaire avec les erreurs
        if (!empty($errors)) {
            $this->view('template/create', [
                'title' => 'Créer un nouvel élément',
                'errors' => $errors,
                'name' => $name,
                'description' => $description
            ]);
            return;
        }
        
        // Créer l'élément
        $templateModel = new TemplateModel();
        $id = $templateModel->create([
            'name' => $name,
            'description' => $description
        ]);
        
        if ($id) {
            // Rediriger avec un message de succès
            $_SESSION['success'] = 'Élément créé avec succès';
            $this->redirect('template');
        } else {
            // Afficher une erreur
            $this->view('template/create', [
                'title' => 'Créer un nouvel élément',
                'error' => 'Une erreur est survenue lors de la création',
                'name' => $name,
                'description' => $description
            ]);
        }
    }
    
    /**
     * Affiche le formulaire de modification
     * 
     * @param int $id ID de l'élément
     */
    public function edit($id) {
        $templateModel = new TemplateModel();
        $item = $templateModel->getById($id);
        
        if (!$item) {
            $_SESSION['error'] = 'Élément non trouvé';
            $this->redirect('template');
            return;
        }
        
        $this->view('template/edit', [
            'title' => 'Modifier un élément',
            'item' => $item
        ]);
    }
    
    /**
     * Traite la soumission du formulaire de modification
     * 
     * @param int $id ID de l'élément
     */
    public function update($id) {
        // Vérifier si la requête est de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('template/edit/' . $id);
            return;
        }
        
        $templateModel = new TemplateModel();
        $item = $templateModel->getById($id);
        
        if (!$item) {
            $_SESSION['error'] = 'Élément non trouvé';
            $this->redirect('template');
            return;
        }
        
        // Récupérer les données du formulaire
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        
        // Valider les données
        $errors = [];
        
        if (empty($name)) {
            $errors['name'] = 'Le nom est requis';
        }
        
        // S'il y a des erreurs, afficher le formulaire avec les erreurs
        if (!empty($errors)) {
            $this->view('template/edit', [
                'title' => 'Modifier un élément',
                'errors' => $errors,
                'item' => array_merge($item, [
                    'name' => $name,
                    'description' => $description
                ])
            ]);
            return;
        }
        
        // Mettre à jour l'élément
        $success = $templateModel->update($id, [
            'name' => $name,
            'description' => $description
        ]);
        
        if ($success) {
            // Rediriger avec un message de succès
            $_SESSION['success'] = 'Élément mis à jour avec succès';
            $this->redirect('template');
        } else {
            // Afficher une erreur
            $this->view('template/edit', [
                'title' => 'Modifier un élément',
                'error' => 'Une erreur est survenue lors de la mise à jour',
                'item' => array_merge($item, [
                    'name' => $name,
                    'description' => $description
                ])
            ]);
        }
    }
    
    /**
     * Supprime un élément
     * 
     * @param int $id ID de l'élément
     */
    public function delete($id) {
        $templateModel = new TemplateModel();
        $item = $templateModel->getById($id);
        
        if (!$item) {
            $_SESSION['error'] = 'Élément non trouvé';
            $this->redirect('template');
            return;
        }
        
        // Supprimer l'élément
        $success = $templateModel->delete($id);
        
        if ($success) {
            $_SESSION['success'] = 'Élément supprimé avec succès';
        } else {
            $_SESSION['error'] = 'Une erreur est survenue lors de la suppression';
        }
        
        $this->redirect('template');
    }
}

