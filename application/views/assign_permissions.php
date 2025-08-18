<h2>Assigner des permissions au rôle : <?= $role['name'] ?></h2>
<form method="post">
    <?php foreach ($permissions as $perm): ?>
        <div class="form-check">
            <input type="checkbox" name="permissions[]" value="<?= $perm['id'] ?>"
                class="form-check-input"
                <?= in_array($perm['id'], $assigned_permissions) ? 'checked' : '' ?>>
            <label class="form-check-label"><?= $perm['name'] ?> (<?= $perm['description'] ?>)</label>
        </div>
    <?php endforeach; ?>
    <button type="submit" class="btn btn-success">Enregistrer les permissions</button>
</form>
