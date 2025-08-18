<h2><?= isset($group) ? 'Modifier' : 'Créer' ?> un groupe</h2>
<form method="post">
    <div class="form-group">
        <label>Nom du groupe</label>
        <input type="text" name="name" class="form-control" value="<?= isset($group) ? $group['name'] : '' ?>" required>
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control"><?= isset($group) ? $group['description'] : '' ?></textarea>
    </div>
    <div class="form-group">
        <label>Permissions</label><br>
        <?php foreach ($permissions as $perm): ?>
            <label>
                <input type="checkbox" name="permissions[]" value="<?= $perm['name'] ?>"
                    <?= isset($group['permissions']) && in_array($perm['name'], $group['permissions']) ? 'checked' : '' ?>>
                <?= $perm['name'] ?>
            </label><br>
        <?php endforeach; ?>
    </div>
    <button type="submit" class="btn btn-primary">Enregistrer</button>
</form>
