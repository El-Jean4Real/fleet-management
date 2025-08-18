<div class="content-header"> 
   <div class="container-fluid">
      <div class="row mb-2">
         <div class="col-sm-6">
            <h1 class="m-0 text-dark">User's List</h1>
         </div>
         <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
               <li class="breadcrumb-item"><a href="<?= base_url(); ?>/dashboard">Dashboard</a></li>
               <li class="breadcrumb-item active">User's List</li>
            </ol>
         </div>
      </div>
   </div>
</div>

<section class="content">
   <div class="container-fluid">
      <div class="card">
         <div class="card-body p-0">
            <div class="table-responsive">
               <form method="post" action="<?= base_url('users/delete_selected_users'); ?>">
                  <table id="custtbl" class="table card-table table-vcenter text-nowrap">
                     <thead>
                        <tr>
                           <th class="w-1">S.No</th>
                           <th><input type="checkbox" id="checkAll"></th>
                           <th>Name</th>
                           <th>Username</th> 
                           <th>Email</th>
                           <th>Status</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if (!empty($userlist)) {
                           $count = 1;
                           foreach ($userlist as $userlists) {
                        ?>
                        <tr>
                           <td><?= output($count++); ?></td>
                           <td><input type="checkbox" name="selected_users[]" value="<?= output($userlists['u_id']); ?>"></td>
                           <td><?= output($userlists['u_name']); ?></td>
                           <td><?= output($userlists['u_username']); ?></td>
                           <td><?= output($userlists['u_email']); ?></td>
                           <td>
                              <span class="badge <?= ($userlists['u_isactive'] == '1') ? 'badge-success' : 'badge-danger'; ?>">
                                 <?= ($userlists['u_isactive'] == '1') ? 'Active' : 'Inactive'; ?>
                              </span>
                           </td>
                           <td>
                              <a class="icon text-primary" href="<?= base_url('users/edituser/' . output($userlists['u_id'])); ?>">
                                 <i class="fa fa-edit"></i>
                              </a>
                              <?php if (userpermission('lr_user_delete')): ?>
                              &nbsp;
                              <a class="icon text-danger" onclick="return confirm('�tes-vous s�r de vouloir supprimer cet utilisateur ?')" 
                                 href="<?= base_url('users/deleteuser/' . output($userlists['u_id'])); ?>">
                                 <i class="fa fa-trash"></i>
                              </a>
                              <?php endif; ?>
                           </td>
                        </tr>
                        <?php } } ?>
                     </tbody>
                  </table>

                  <?php if (userpermission('lr_user_delete')): ?>
                     <div class="p-3">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer les utilisateurs s�lectionn�s ?');">
                           <i class="fa fa-trash"></i> Supprimer la sélection
                        </button>
                     </div>
                  <?php endif; ?>
               </form>
            </div>
         </div>
      </div>
   </div>
</section>

<script>
   // Check/uncheck all checkboxes
   document.getElementById('checkAll').addEventListener('click', function(){
      const checkboxes = document.querySelectorAll('input[name="selected_users[]"]');
      checkboxes.forEach(cb => cb.checked = this.checked);
   });
</script>
