<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="icon" type="image/x-icon" href="<?= APP_URL ?>/media/favicon.ico">
    <style>
        body {
            padding-top: 60px;
            padding-bottom: 40px;
        }
        .alert-container {
            position: fixed;
            top: 70px;
            right: 20px;
            z-index: 9999;
            max-width: 350px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?= APP_URL ?>">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="50" height="50"><ellipse cx="100" cy="120" rx="50" ry="35"/><circle cx="100" cy="70" r="30"/><circle cx="115" cy="60" r="5" fill="#fff"/><path fill="red" d="m130 70-10 10 10 3zm20 45c-132-35 20-15 0 5"/></svg>

            <?= APP_NAME ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>">Accueil</a>
                    </li>
                    <?php if (Auth::isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/user">Utilisateurs</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (Auth::isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($_SESSION['username']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="<?= APP_URL ?>/auth/logout">Déconnexion</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                      <button type="button" class="nav-link" data-bs-toggle="modal" data-bs-target="#loginModal">
                      Connexion
                      </button>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/auth/register">Inscription</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Affichage des messages flash -->
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert-container">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert-container">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>




<!-- Modal de connexion -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="loginModalLabel">Connexion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="loginError" class="alert alert-danger d-none"></div>
                
                <form id="loginForm" action="<?= APP_URL ?>/auth/authenticate" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <div class="invalid-feedback" id="usernameError"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="invalid-feedback" id="passwordError"></div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Se connecter</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <p class="mb-0">Vous n'avez pas de compte ? <a href="<?= APP_URL ?>/auth/register">S'inscrire</a></p>
            </div>
        </div>
    </div>
</div>

<!-- Script pour gérer la soumission du formulaire de connexion en AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const loginError = document.getElementById('loginError');
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Réinitialiser les messages d'erreur
        loginError.classList.add('d-none');
        loginError.textContent = '';
        document.getElementById('username').classList.remove('is-invalid');
        document.getElementById('password').classList.remove('is-invalid');
        document.getElementById('usernameError').textContent = '';
        document.getElementById('passwordError').textContent = '';
        
        // Récupérer les données du formulaire
        const formData = new FormData(loginForm);
        
        // Envoyer la requête AJAX
        fetch(loginForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirection en cas de succès
                window.location.href = data.redirect;
            } else {
                // Afficher les erreurs
                if (data.error) {
                    loginError.textContent = data.error;
                    loginError.classList.remove('d-none');
                }
                
                if (data.errors) {
                    if (data.errors.username) {
                        document.getElementById('username').classList.add('is-invalid');
                        document.getElementById('usernameError').textContent = data.errors.username;
                    }
                    
                    if (data.errors.password) {
                        document.getElementById('password').classList.add('is-invalid');
                        document.getElementById('passwordError').textContent = data.errors.password;
                    }
                }
            }
        })
        .catch(error => {
            loginError.textContent = 'Une erreur est survenue lors de la connexion';
            loginError.classList.remove('d-none');
        });
    });
});
</script>




