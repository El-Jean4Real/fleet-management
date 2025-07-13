<div class="container mt-5">
    <h2 class="mb-4"><?= isset($edit_mode) ? 'Modifier' : 'Ajouter' ?> un Objectif</h2>
    <form method="post" action="<?= site_url('objectifs/' . (isset($edit_mode) ? 'edit/' . $objectifs_data['id'] : 'add')) ?>">

        <div class="form-group">
            <label for="type_cible">Type de cible</label>
            <select name="type_cible" id="type_cible" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                <option value="vehicule" <?= (isset($objectifs_data['type_cible']) && $objectifs_data['type_cible'] == 'vehicule') ? 'selected' : '' ?>>Véhicule</option>
                <option value="user" <?= (isset($objectifs_data['type_cible']) && $objectifs_data['type_cible'] == 'user') ? 'selected' : '' ?>>Chauffeur</option>
            </select>
        </div>

        <div class="form-group" id="vehicule_block" style="display:none;">
            <label for="vehicule">Sélectionner un véhicule</label>
            <select name="cible_id_vehicule" id="vehicule" class="form-control">
                <?php foreach ($vehicules as $v): ?>
                    <option value="<?= $v['v_id'] ?>" <?= (isset($objectifs_data['cible_id']) && $objectifs_data['cible_id'] == $v['v_id']) ? 'selected' : '' ?>>
                        <?= $v['v_registration_no'] ?> - <?= $v['v_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" id="chauffeur_block" style="display:none;">
            <label for="chauffeur">Sélectionner un chauffeur</label>
            <select name="cible_id_chauffeur" id="chauffeur" class="form-control">
                <?php foreach ($chauffeurs as $c): ?>
                    <option value="<?= $c['d_id'] ?>" <?= (isset($objectifs_data['cible_id']) && $objectifs_data['cible_id'] == $c['d_id']) ? 'selected' : '' ?>>
                        <?= $c['d_name'] ?> (<?= $c['d_mobile'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

	<div class="form-group">
	    <label for="periode_type">Période</label>
	    <select name="periode_type" class="form-control" required>
        	<option value="mensuelle">Mensuelle</option>
	        <option value="hebdomadaire">Hebdomadaire</option>
	    </select>
	</div>


        <div class="form-group">
            <label for="periode_debut">Date de début</label>
            <input type="date" name="periode_debut" class="form-control" 
                   value="<?= isset($objectifs_data['periode_debut']) ? $objectifs_data['periode_debut'] : '' ?>" required>
        </div>

        <div class="form-group">
            <label for="periode_fin">Date de fin</label>
            <input type="date" name="periode_fin" class="form-control" 
                   value="<?= isset($objectifs_data['periode_fin']) ? $objectifs_data['periode_fin'] : '' ?>" required>
        </div>

        <div class="form-group">
            <label for="montant_objectif">Montant objectif (FCFA)</label>
            <input type="number" name="montant_objectif" class="form-control" 
                   value="<?= isset($objectifs_data['montant_objectif']) ? $objectifs_data['montant_objectif'] : '' ?>" required>
        </div>

        <input type="hidden" name="cible_id" id="cible_id_hidden" value="<?= isset($objectifs_data['cible_id']) ? $objectifs_data['cible_id'] : '' ?>">

        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="<?= site_url('objectifs') ?>" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<script>
// Script initial pour afficher le bon bloc au chargement
document.addEventListener('DOMContentLoaded', function() {
    const typeCible = document.getElementById('type_cible').value;
    if (typeCible === 'vehicule') {
        document.getElementById('vehicule_block').style.display = 'block';
        document.getElementById('cible_id_hidden').value = document.getElementById('vehicule').value;
    } else if (typeCible === 'user') {
        document.getElementById('chauffeur_block').style.display = 'block';
        document.getElementById('cible_id_hidden').value = document.getElementById('chauffeur').value;
    }
});

// Gestion du changement de type
document.getElementById('type_cible').addEventListener('change', function () {
    const type = this.value;
    const vehiculeBlock = document.getElementById('vehicule_block');
    const chauffeurBlock = document.getElementById('chauffeur_block');
    const cibleId = document.getElementById('cible_id_hidden');

    if (type === 'vehicule') {
        vehiculeBlock.style.display = 'block';
        chauffeurBlock.style.display = 'none';
        cibleId.value = document.getElementById('vehicule').value;
    } else if (type === 'user') {
        vehiculeBlock.style.display = 'none';
        chauffeurBlock.style.display = 'block';
        cibleId.value = document.getElementById('chauffeur').value;
    } else {
        vehiculeBlock.style.display = 'none';
        chauffeurBlock.style.display = 'none';
        cibleId.value = '';
    }
});

// Mise à jour de l'ID caché quand on change la sélection
document.getElementById('vehicule')?.addEventListener('change', function () {
    document.getElementById('cible_id_hidden').value = this.value;
});
document.getElementById('chauffeur')?.addEventListener('change', function () {
    document.getElementById('cible_id_hidden').value = this.value;
});
// Forcer une mise à jour au chargement même si aucun type sélectionné
setTimeout(function() {
    const type = document.getElementById('type_cible').value;
    const cibleId = document.getElementById('cible_id_hidden');

    if (type === 'vehicule') {
        cibleId.value = document.getElementById('vehicule').value;
    } else if (type === 'user') {
        cibleId.value = document.getElementById('chauffeur').value;
    }
}, 300);

</script>