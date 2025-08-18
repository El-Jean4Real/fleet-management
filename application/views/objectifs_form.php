<div class="container mt-5">
    <h2 class="mb-4"><?= isset($edit_mode) ? 'Modifier' : 'Ajouter' ?> un Objectif</h2>
    <form method="post" action="<?= site_url('objectifs/' . (isset($edit_mode) ? 'edit/' . $objectifs_data['id'] : 'add')) ?>">

        <div class="form-group">
            <label for="type_cible">Type de cible</label>
            <select name="type_cible" id="type_cible" class="form-control" required>
                <option value="">-- Sélectionner --</option>
                <option value="vehicule" <?= (isset($objectifs_data['type_cible']) && $objectifs_data['type_cible'] == 'vehicule') ? 'selected' : '' ?>>Véhicule</option>
                <option value="groupe" <?= (isset($objectifs_data['type_cible']) && $objectifs_data['type_cible'] == 'groupe') ? 'selected' : '' ?>>Groupe de véhicules</option>
                <option value="user" <?= (isset($objectifs_data['type_cible']) && $objectifs_data['type_cible'] == 'user') ? 'selected' : '' ?>>Chauffeur</option>
            </select>
        </div>

        <!-- Véhicule -->
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

        <!-- Groupe -->
        <div class="form-group" id="groupe_block" style="display:none;">
            <label for="groupe">Sélectionner un groupe de véhicules</label>
            <select name="cible_id_groupe" id="groupe" class="form-control">
                <?php if (!empty($vehicule_group)): ?>
                    <?php foreach ($vehicule_group as $g): ?>
                        <option value="<?= $g['gr_id'] ?>" <?= (isset($objectifs_data['cible_id']) && $objectifs_data['cible_id'] == $g['gr_id']) ? 'selected' : '' ?>>
                            <?= $g['gr_name'] ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option>Aucun groupe disponible</option>
                <?php endif; ?>
            </select>
        </div>


        <!-- Chauffeur -->
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
                <option value="mensuelle" <?= isset($objectifs_data['periode_type']) && $objectifs_data['periode_type'] == 'mensuelle' ? 'selected' : '' ?>>Mensuelle</option>
                <option value="hebdomadaire" <?= isset($objectifs_data['periode_type']) && $objectifs_data['periode_type'] == 'hebdomadaire' ? 'selected' : '' ?>>Hebdomadaire</option>
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

        <!-- Champ caché final envoyé en POST -->
        <input type="hidden" name="cible_id" id="cible_id_hidden" value="<?= isset($objectifs_data['cible_id']) ? $objectifs_data['cible_id'] : '' ?>">

        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="<?= site_url('objectifs') ?>" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeCible = document.getElementById('type_cible');
    const cibleIdHidden = document.getElementById('cible_id_hidden');
    const vehiculeBlock = document.getElementById('vehicule_block');
    const groupeBlock = document.getElementById('groupe_block');
    const chauffeurBlock = document.getElementById('chauffeur_block');

    const vehiculeSelect = document.getElementById('vehicule');
    const groupeSelect = document.getElementById('groupe');
    const chauffeurSelect = document.getElementById('chauffeur');

    function updateVisibility() {
        vehiculeBlock.style.display = 'none';
        groupeBlock.style.display = 'none';
        chauffeurBlock.style.display = 'none';

        if (typeCible.value === 'vehicule') {
            vehiculeBlock.style.display = 'block';
            cibleIdHidden.value = vehiculeSelect.value;
        } else if (typeCible.value === 'groupe') {
            groupeBlock.style.display = 'block';
            cibleIdHidden.value = groupeSelect.value;
        } else if (typeCible.value === 'user') {
            chauffeurBlock.style.display = 'block';
            cibleIdHidden.value = chauffeurSelect.value;
        } else {
            cibleIdHidden.value = '';
        }
    }

    // Initialisation
    updateVisibility();

    // Événements
    typeCible.addEventListener('change', updateVisibility);
    vehiculeSelect?.addEventListener('change', () => cibleIdHidden.value = vehiculeSelect.value);
    groupeSelect?.addEventListener('change', () => cibleIdHidden.value = groupeSelect.value);
    chauffeurSelect?.addEventListener('change', () => cibleIdHidden.value = chauffeurSelect.value);
});
</script>
