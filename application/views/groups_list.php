<h2>Liste des groupes</h2>
<a href="<?= base_url('groups/create') ?>" class="btn btn-success">+ Nouveau Groupe</a>
<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($groups as $g): ?>
        <tr>
            <td><?= $g['id'] ?></td>
            <td><?= $g['name'] ?></td>
            <td><?= $g['description'] ?></td>
            <td>
                <a href="<?= base_url('groups/edit/'.$g['id']) ?>" class="btn btn-warning btn-sm">Modifier</a>
                <a href="<?= base_url('groups/delete/'.$g['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce groupe ?')">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
