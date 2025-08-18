<?php
// Par sécurité pour les notices
$v = isset($userdetails[0]) ? $userdetails[0] : [];
?>
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">
          <?php echo (isset($userdetails)) ? 'Edit User' : 'Add User' ?>
        </h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?= base_url(); ?>/dashboard">User</a></li>
          <li class="breadcrumb-item active"><?php echo (isset($userdetails)) ? 'Edit User' : 'Add User' ?></li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">

    <form method="post"
          action="<?= base_url(isset($v['u_id']) ? 'users/updateuser' : 'users/insertuser'); ?>"
          enctype="multipart/form-data">

      <div class="card-body">

        <div class="row">
          <input type="hidden" name="basic[u_id]" id="u_id" value="<?= isset($v['u_id']) ? $v['u_id'] : '' ?>">

          <div class="col-sm-6 col-md-4">
            <label class="form-label">Name</label>
            <div class="form-group">
              <input type="text" name="basic[u_name]" id="u_name" required class="form-control" placeholder="Name"
                     value="<?= isset($v['u_name']) ? $v['u_name'] : '' ?>">
            </div>
          </div>

          <div class="col-sm-6 col-md-4">
            <label class="form-label">Email</label>
            <div class="form-group">
              <input type="text" name="basic[u_email]" id="u_email" required class="form-control" placeholder="Email"
                     value="<?= isset($v['u_email']) ? $v['u_email'] : '' ?>">
            </div>
          </div>

          <div class="col-sm-6 col-md-4">
            <label class="form-label">User Name</label>
            <div class="form-group">
              <input type="text" name="basic[u_username]" id="u_username" required class="form-control" placeholder="User Name"
                     value="<?= isset($v['u_username']) ? $v['u_username'] : '' ?>">
            </div>
          </div>

          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label class="form-label">Password</label>
              <input type="password" name="basic[u_password]" class="form-control"
                     placeholder="<?= isset($v['u_id']) ? 'Laisser vide pour ne pas changer' : 'Password' ?>">
            </div>
          </div>

          <?php if (isset($v['u_isactive'])): ?>
            <div class="col-sm-6 col-md-4">
              <div class="form-group">
                <label for="u_isactive" class="form-label">User Status</label>
                <select id="u_isactive" name="basic[u_isactive]" class="form-control" required>
                  <option value="">Select User Status</option>
                  <option value="1" <?= (isset($v['u_isactive']) && (int)$v['u_isactive'] === 1) ? 'selected' : '' ?>>Active</option>
                  <option value="0" <?= (isset($v['u_isactive']) && (int)$v['u_isactive'] === 0) ? 'selected' : '' ?>>Inactive</option>
                </select>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <hr>
        <div class="form-label"><b>User Permission's</b></div><br>

        <!-- Vehicle -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Vehicle</label></div></div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_vech_list'])) ? 'checked' : '' ?>
                     name="permissions[lr_vech_list]" class="custom-control-input" id="lr_vech_list">
              <label class="custom-control-label" for="lr_vech_list">All List</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_vech_list_view'])) ? 'checked' : '' ?>
                     name="permissions[lr_vech_list_view]" class="custom-control-input" id="lr_vech_list_view">
              <label class="custom-control-label" for="lr_vech_list_view">Detail View</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_vech_list_edit'])) ? 'checked' : '' ?>
                     name="permissions[lr_vech_list_edit]" class="custom-control-input" id="lr_vech_list_edit">
              <label class="custom-control-label" for="lr_vech_list_edit">Edit</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_vech_add'])) ? 'checked' : '' ?>
                     name="permissions[lr_vech_add]" class="custom-control-input" id="lr_vech_add">
              <label class="custom-control-label" for="lr_vech_add">Add</label>
            </div>
          </div>
        </div>

        <!-- Vehicle Group -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Vehicle Group</label></div></div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_vech_group'])) ? 'checked' : '' ?>
                     name="permissions[lr_vech_group]" class="custom-control-input" id="lr_vech_group">
              <label class="custom-control-label" for="lr_vech_group">All List</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_vech_group_add'])) ? 'checked' : '' ?>
                     name="permissions[lr_vech_group_add]" class="custom-control-input" id="lr_vech_group_add">
              <label class="custom-control-label" for="lr_vech_group_add">Add New</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_vech_group_action'])) ? 'checked' : '' ?>
                     name="permissions[lr_vech_group_action]" class="custom-control-input" id="lr_vech_group_action">
              <label class="custom-control-label" for="lr_vech_group_action">Delete</label>
            </div>
          </div>
        </div>

        <!-- Driver -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Driver</label></div></div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_drivers_list'])) ? 'checked' : '' ?>
                     name="permissions[lr_drivers_list]" class="custom-control-input" id="lr_drivers_list">
              <label class="custom-control-label" for="lr_drivers_list">All List</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_drivers_list_edit'])) ? 'checked' : '' ?>
                     name="permissions[lr_drivers_list_edit]" class="custom-control-input" id="lr_drivers_list_edit">
              <label class="custom-control-label" for="lr_drivers_list_edit">Edit</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_drivers_add'])) ? 'checked' : '' ?>
                     name="permissions[lr_drivers_add]" class="custom-control-input" id="lr_drivers_add">
              <label class="custom-control-label" for="lr_drivers_add">Add New</label>
            </div>
          </div>
        </div>

        <!-- Bookings -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Bookings</label></div></div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_trips_list'])) ? 'checked' : '' ?>
                     name="permissions[lr_trips_list]" class="custom-control-input" id="lr_trips_list">
              <label class="custom-control-label" for="lr_trips_list">All Bookings</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_trips_list_edit'])) ? 'checked' : '' ?>
                     name="permissions[lr_trips_list_edit]" class="custom-control-input" id="lr_trips_list_edit">
              <label class="custom-control-label" for="lr_trips_list_edit">Edit</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_trips_add'])) ? 'checked' : '' ?>
                     name="permissions[lr_trips_add]" class="custom-control-input" id="lr_trips_add">
              <label class="custom-control-label" for="lr_trips_add">Add New</label>
            </div>
          </div>
        </div>

        <!-- Customer -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Customer</label></div></div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_cust_list'])) ? 'checked' : '' ?>
                     name="permissions[lr_cust_list]" class="custom-control-input" id="lr_cust_list">
              <label class="custom-control-label" for="lr_cust_list">All List</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_cust_edit'])) ? 'checked' : '' ?>
                     name="permissions[lr_cust_edit]" class="custom-control-input" id="lr_cust_edit">
              <label class="custom-control-label" for="lr_cust_edit">Edit</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_cust_add'])) ? 'checked' : '' ?>
                     name="permissions[lr_cust_add]" class="custom-control-input" id="lr_cust_add">
              <label class="custom-control-label" for="lr_cust_add">Add New</label>
            </div>
          </div>
        </div>

        <!-- Fuel -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Fuel</label></div></div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_fuel_list'])) ? 'checked' : '' ?>
                     name="permissions[lr_fuel_list]" class="custom-control-input" id="lr_fuel_list">
              <label class="custom-control-label" for="lr_fuel_list">Fuel List</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_fuel_edit'])) ? 'checked' : '' ?>
                     name="permissions[lr_fuel_edit]" class="custom-control-input" id="lr_fuel_edit">
              <label class="custom-control-label" for="lr_fuel_edit">Edit</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_fuel_add'])) ? 'checked' : '' ?>
                     name="permissions[lr_fuel_add]" class="custom-control-input" id="lr_fuel_add">
              <label class="custom-control-label" for="lr_fuel_add">Add New</label>
            </div>
          </div>
        </div>

        <!-- Reminder -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Reminder</label></div></div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_reminder_list'])) ? 'checked' : '' ?>
                     name="permissions[lr_reminder_list]" class="custom-control-input" id="lr_reminder_list">
              <label class="custom-control-label" for="lr_reminder_list">All List</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_reminder_delete'])) ? 'checked' : '' ?>
                     name="permissions[lr_reminder_delete]" class="custom-control-input" id="lr_reminder_delete">
              <label class="custom-control-label" for="lr_reminder_delete">Delete</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_reminder_add'])) ? 'checked' : '' ?>
                     name="permissions[lr_reminder_add]" class="custom-control-input" id="lr_reminder_add">
              <label class="custom-control-label" for="lr_reminder_add">Add New</label>
            </div>
          </div>
        </div>

        <!-- Income Expense (désormais 100% indépendant) -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Income Expense</label></div></div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_ie_list'])) ? 'checked' : '' ?>
                     name="permissions[lr_ie_list]" class="custom-control-input" id="lr_ie_list">
              <label class="custom-control-label" for="lr_ie_list">All List</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_ie_edit'])) ? 'checked' : '' ?>
                     name="permissions[lr_ie_edit]" class="custom-control-input" id="lr_ie_edit">
              <label class="custom-control-label" for="lr_ie_edit">Edit</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_ie_add'])) ? 'checked' : '' ?>
                     name="permissions[lr_ie_add]" class="custom-control-input" id="lr_ie_add">
              <label class="custom-control-label" for="lr_ie_add">Add New</label>
            </div>
          </div>
        </div>

        <!-- Objectifs -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Objectifs</label></div></div>
          <?php
            $obj_keys = ['lr_objectifs_view' => 'Voir', 'lr_objectifs_add' => 'Ajouter', 'lr_objectifs_edit' => 'Modifier', 'lr_objectifs_delete' => 'Supprimer', 'lr_objectifs_stats' => 'Statistiques'];
            foreach ($obj_keys as $key => $label): ?>
            <div class="form-group mr-4">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" value="1"
                       <?= (!empty($v[$key])) ? 'checked' : '' ?>
                       name="permissions[<?= $key ?>]" class="custom-control-input" id="<?= $key ?>">
                <label class="custom-control-label" for="<?= $key ?>"><?= $label ?></label>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Recettes -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Recettes</label></div></div>
          <?php
            $recette_keys = ['lr_recette_view' => 'Voir', 'lr_recette_add' => 'Ajouter', 'lr_recette_edit' => 'Modifier', 'lr_recette_delete' => 'Supprimer'];
            foreach ($recette_keys as $key => $label): ?>
            <div class="form-group mr-4">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" value="1"
                       <?= (!empty($v[$key])) ? 'checked' : '' ?>
                       name="permissions[<?= $key ?>]" class="custom-control-input" id="<?= $key ?>">
                <label class="custom-control-label" for="<?= $key ?>"><?= $label ?></label>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Notifications -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Notifications</label></div></div>
          <?php
            $notif_keys = ['lr_notifications_view' => 'Voir', 'lr_notifications_manage' => 'Gerer'];
            foreach ($notif_keys as $key => $label): ?>
            <div class="form-group mr-4">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" value="1"
                       <?= (!empty($v[$key])) ? 'checked' : '' ?>
                       name="permissions[<?= $key ?>]" class="custom-control-input" id="<?= $key ?>">
                <label class="custom-control-label" for="<?= $key ?>"><?= $label ?></label>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Utilisateurs -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Utilisateurs</label></div></div>
          <?php
            $user_keys = ['lr_user_list' => 'Liste', 'lr_user_add' => 'Ajouter', 'lr_user_edit_roles' => 'Modifier roles'];
            foreach ($user_keys as $key => $label): ?>
            <div class="form-group mr-4">
              <div class="custom-control custom-checkbox">
                <input type="checkbox" value="1"
                       <?= (!empty($v[$key])) ? 'checked' : '' ?>
                       name="permissions[<?= $key ?>]" class="custom-control-input" id="<?= $key ?>">
                <label class="custom-control-label" for="<?= $key ?>"><?= $label ?></label>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Tracking -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Tracking</label></div></div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_tracking'])) ? 'checked' : '' ?>
                     name="permissions[lr_tracking]" class="custom-control-input" id="lr_tracking">
              <label class="custom-control-label" for="lr_tracking">History Tracking</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_liveloc'])) ? 'checked' : '' ?>
                     name="permissions[lr_liveloc]" class="custom-control-input" id="lr_liveloc">
              <label class="custom-control-label" for="lr_liveloc">Live Location</label>
            </div>
          </div>
        </div>

        <!-- Geofence -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Geofence</label></div></div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_geofence_add'])) ? 'checked' : '' ?>
                     name="permissions[lr_geofence_add]" class="custom-control-input" id="lr_geofence_add">
              <label class="custom-control-label" for="lr_geofence_add">Add</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_geofence_list'])) ? 'checked' : '' ?>
                     name="permissions[lr_geofence_list]" class="custom-control-input" id="lr_geofence_list">
              <label class="custom-control-label" for="lr_geofence_list">All List</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_geofence_delete'])) ? 'checked' : '' ?>
                     name="permissions[lr_geofence_delete]" class="custom-control-input" id="lr_geofence_delete">
              <label class="custom-control-label" for="lr_geofence_delete">Delete</label>
            </div>
          </div>

          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_geofence_events'])) ? 'checked' : '' ?>
                     name="permissions[lr_geofence_events]" class="custom-control-input" id="lr_geofence_events">
              <label class="custom-control-label" for="lr_geofence_events">Events</label>
            </div>
          </div>
        </div>

        <!-- Reports -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Reports</label></div></div>
          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_reports'])) ? 'checked' : '' ?>
                     name="permissions[lr_reports]" class="custom-control-input" id="lr_reports">
              <label class="custom-control-label" for="lr_reports">View Reports</label>
            </div>
          </div>
        </div>

        <!-- Settings -->
        <div class="row">
          <div class="col-sm-6 col-md-2"><div class="form-group"><label class="form-label">Settings</label></div></div>
          <div class="form-group mr-4">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" value="1"
                     <?= (!empty($v['lr_settings'])) ? 'checked' : '' ?>
                     name="permissions[lr_settings]" class="custom-control-input" id="lr_settings">
              <label class="custom-control-label" for="lr_settings">All Settings</label>
            </div>
          </div>
        </div>

        <!-- Avatar -->
        <div class="col-sm-6 col-md-4">
          <label class="form-label">Photo de profil</label>
          <div class="form-group">
            <input type="file" name="u_photo" class="form-control">
            <?php if (!empty($v['u_photo'])): ?>
              <br>
              <img src="<?= base_url('uploads/user_photos/' . $v['u_photo']); ?>" alt="Photo de profil" width="100">
            <?php endif; ?>
          </div>
        </div>

      </div><!-- /.card-body -->

      <div class="card-footer text-right">
        <a href="<?= base_url('users'); ?>" class="btn btn-secondary mr-2">Annuler</a>

        <?php if (isset($v['u_id']) && userpermission('lr_user_delete')): ?>
          <a href="<?= base_url('users/deleteuser/' . $v['u_id']); ?>"
             class="btn btn-danger mr-2"
             onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
             Supprimer
          </a>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary">
          <?= isset($userdetails) ? 'Update User' : 'Add User'; ?>
        </button>
      </div>

    </form>

  </div>
</section>
