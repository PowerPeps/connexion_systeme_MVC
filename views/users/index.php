<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Gestion des utilisateurs</h1>
    <a href="<?= APP_URL ?>/user/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nouvel utilisateur
    </a>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom d'utilisateur</th>
                        <th>Email</th>
                        <th>Admin</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Aucun utilisateur trouvé</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['mail']) ?></td>
                                <td>
                                    <?php if ($user['is_admin']): ?>
                                        <span class="badge bg-success">Oui</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Non</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= APP_URL ?>/user/edit/<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Modifier
                                        </a>
                                        <?php if ($user['id'] != Auth::getUserId()): ?>
                                            <a href="<?= APP_URL ?>/user/delete/<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                <i class="bi bi-trash"></i> Supprimer
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

