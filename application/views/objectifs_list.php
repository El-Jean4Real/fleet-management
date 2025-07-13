<div class="container mt-5">

    <h2><?= $this->lang->line('objectives'); ?></h2>


    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
    <?php elseif ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
    <?php endif; ?>

    <div class="mb-3 text-right">
        <a href="<?= site_url('objectifs/add') ?>" class="btn btn-primary">🎯Ajouter un Objectif</a>
    </div>

    <?php if (empty($objectifs)): ?>
        <div class="alert alert-info">Aucun objectif enregisté pour le moment.</div>
    <?php else: ?>
        <div class="table-responsive">

	   <div class="card mb-3 p-3">
	       <form method="get" class="form-inline row">
	           <div class="form-group col-md-3">
           	    <label>Type de cible :</label>
	               <select name="type_cible" class="form-control ml-2">
           	        <option value="">-- Tous --</option>
	                   <option value="vehicule" <?= set_select('type_cible', 'vehicule', $this->input->get('type_cible') == 'vehicule') ?>>Véhicule</option>
           	        <option value="user" <?= set_select('type_cible', 'user', $this->input->get('type_cible') == 'user') ?>>Chauffeur</option>
	               </select>
	           </div>

	           <div class="form-group col-md-3">
           	    <label>Cible :</label>
	               <select name="cible_id" class="form-control ml-2">
           	        <option value="">-- Tous --</option>
	                   <?php foreach (array_merge($vehicules, $chauffeurs) as $c): ?>
           	            <?php $isVehicule = isset($c['v_id']); ?>
                   	    <option value="<?= $isVehicule ? $c['v_id'] : $c['d_id'] ?>" <?= $this->input->get('cible_id') == ($isVehicule ? $c['v_id'] : $c['d_id']) ? 'selected' : '' ?>>
	                           <?= $isVehicule ? '?? '.$c['v_registration_no'].' - '.$c['v_name'] : '?? '.$c['d_name'].' ('.$c['d_mobile'].')' ?>
           	            </option>
                	   <?php endforeach; ?>
	               </select>
	           </div>

	           <div class="form-group col-md-2">
           	    <label>Date début :</label>
	               <input type="date" name="periode_debut" value="<?= html_escape($this->input->get('periode_debut')) ?>" class="form-control ml-2">
	           </div>

	           <div class="form-group col-md-2">
           	    <label>Date fin :</label>
	               <input type="date" name="periode_fin" value="<?= html_escape($this->input->get('periode_fin')) ?>" class="form-control ml-2">
	           </div>

	           <div class="form-group col-md-2 mt-4">
           	    <button type="submit" class="btn btn-primary">Filtrer</button>
	               <a href="<?= site_url('objectifs') ?>" class="btn btn-secondary">Réinitialiser</a>
	           </div>
	       </form>
	   </div>

	    
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Type de cible</th>
                        <th>Nom de la cible</th>
                        <th>Période</th>
                        <th>Montant Objectif</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($objectifs as $index => $obj): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= ucfirst($obj['type_cible']) ?></td>
                            <td>
                                <?php if ($obj['type_cible'] == 'vehicule'): ?>
                                    <?php
                                        $vehicule = $this->db->get_where('vehicles', ['v_id' => $obj['cible_id']])->row_array();
                                        echo $vehicule ? $vehicule['v_registration_no'] . ' - ' . $vehicule['v_name'] : 'N/A';
                                    ?>
                                <?php elseif ($obj['type_cible'] == 'user'): ?>
                                    <?php
                                        $chauffeur = $this->db->get_where('drivers', ['d_id' => $obj['cible_id']])->row_array();
                                        echo $chauffeur ? $chauffeur['d_name'] . ' (' . $chauffeur['d_mobile'] . ')' : 'N/A';
                                    ?>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($obj['periode_debut'])) ?> au <?= date('d/m/Y', strtotime($obj['periode_fin'])) ?></td>
                            <td><?= number_format($obj['montant_objectif'], 0, ',', ' ') ?> FCFA</td>
                            <td>
                                <a href="<?= site_url('objectifs/edit/' . $obj['id']) ?>" class="btn btn-sm btn-warning">✍🏾Modifier</a>
                                <a href="<?= site_url('objectifs/delete/' . $obj['id']) ?>"
                                   onclick="return confirm('Voulez-vous vraiment supprimer cet objectif ?');"
                                   class="btn btn-sm btn-danger">⚠️Supprimer</a>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
