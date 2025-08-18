<h2>Créer une nouvelle permission</h2>
<form method="post" action="<?= base_url('Permission/create') ?>">
    <div class="form-group">
        <label for="name">Nom de la permission</label>
        <input type="text" name="name" class="form-control" required />
    </div>
    <div class="form-group">
        <label for="description">Description (facultatif)</label>
        <textarea name="description" class="form-control"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Enregistrer</button>
</form>
