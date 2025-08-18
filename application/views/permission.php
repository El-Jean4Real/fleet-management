<h2>Liste des permissions</h2>
<a href="<?= base_url('Permission/create') ?>" class="btn btn-success">Ajouter une permission</a>
<table class="table">
    <thead>
        <tr><th>ID</th><th>Nom</th><th>Description</th></tr>
    </thead>
    <tbody>
        <?php foreach ($permissions as $perm): ?>
            <tr>
                <td><?= $perm['id'] ?></td>
                <td><?= $perm['name'] ?></td>
                <td><?= $perm['description'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
