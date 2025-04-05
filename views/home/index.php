<div class="px-4 py-5 my-5 text-center">
    <h1 class="display-5 fw-bold"><?= APP_NAME ?></h1>
    <div class="col-lg-6 mx-auto">
        <p class="lead mb-4">
            Bienvenue sur votre framework MVC maison. Ce framework simple et léger vous permet de créer rapidement des applications web en PHP.
        </p>
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <?php if (Auth::isLoggedIn()): ?>
                <p class="fs-5">Vous êtes connecté en tant que <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>.</p>
            <?php else: ?>
                <a href="<?= APP_URL ?>/auth/register" class="btn btn-outline-secondary btn-lg px-4">Inscription</a>
                <a class="btn btn-primary btn-lg px-4 gap-3" href="<?= APP_URL ?>/auth/login">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</div>
