<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Gestion des éléments</h1>
    <a href="<?= APP_URL ?>/template/create" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Nouvel élément
    </a>
</div>

<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Description</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="5" class="text-center">Aucun élément trouvé</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= $item['id'] ?></td>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= htmlspecialchars($item['description'] ?? '') ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= APP_URL ?>/template/edit/<?= $item['id'] ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Modifier
                                        </a>
                                        <a href="<?= APP_URL ?>/template/delete/<?= $item['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </a>
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

