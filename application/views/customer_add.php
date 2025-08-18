<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark"><?= (isset($customerdetails)) ? lang('edit_customer') : lang('add_customer') ?></h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?= base_url(); ?>/dashboard"><?= lang('customer') ?></a></li>
          <li class="breadcrumb-item active"><?= (isset($customerdetails)) ? lang('edit_customer') : lang('add_customer') ?></li>
        </ol>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <form method="post" id="customer_add" class="card" action="<?= base_url(); ?>customer/<?= (isset($customerdetails)) ? 'updatecustomer' : 'insertcustomer'; ?>">
      <div class="card-body">
        <div class="row">
          <input type="hidden" name="c_id" id="c_id" value="<?= isset($customerdetails) ? $customerdetails[0]['c_id'] : '' ?>">

          <div class="col-sm-6 col-md-3">
            <div class="form-group">
              <label class="form-label"><?= lang('name') ?><span class="form-required">*</span></label>
              <input type="text" required class="form-control" value="<?= isset($customerdetails) ? $customerdetails[0]['c_name'] : '' ?>" id="c_name" name="c_name" placeholder="<?= lang('customer_name') ?>">
            </div>
          </div>

          <div class="col-sm-6 col-md-3">
            <div class="form-group">
              <label class="form-label"><?= lang('mobile') ?><span class="form-required">*</span></label>
              <input type="text" required class="form-control" value="<?= isset($customerdetails) ? $customerdetails[0]['c_mobile'] : '' ?>" id="c_mobile" name="c_mobile" placeholder="<?= lang('customer_mobile') ?>">
            </div>
          </div>

          <div class="col-sm-6 col-md-4">
            <div class="form-group">
              <label class="form-label"><?= lang('email') ?></label>
              <input type="text" class="form-control" value="<?= isset($customerdetails) ? $customerdetails[0]['c_email'] : '' ?>" id="c_email" name="c_email" placeholder="<?= lang('customer_email') ?>">
            </div>
          </div>

          <?php if (isset($customerdetails[0]['c_isactive'])): ?>
          <div class="col-sm-6 col-md-2">
            <div class="form-group">
              <label class="form-label"><?= lang('customer_status') ?></label>
              <select id="c_isactive" name="c_isactive" class="form-control" required>
                <option value=""><?= lang('select_status') ?></option>
                <option <?= ($customerdetails[0]['c_isactive']==1) ? 'selected' : '' ?> value="1"><?= lang('active') ?></option>
                <option <?= ($customerdetails[0]['c_isactive']==0) ? 'selected' : '' ?> value="0"><?= lang('inactive') ?></option>
              </select>
            </div>
          </div>
          <?php endif; ?>

          <div class="col-sm-6 col-md-6">
            <div class="form-group">
              <label class="form-label"><?= lang('address') ?><span class="form-required">*</span></label>
              <textarea class="form-control" required id="c_address" placeholder="<?= lang('address') ?>" name="c_address"><?= isset($customerdetails) ? $customerdetails[0]['c_address'] : '' ?></textarea>
            </div>
          </div>
        </div>

        <input type="hidden" id="c_created_date" name="c_created_date" value="<?= date('Y-m-d h:i:s'); ?>">

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><?= (isset($customerdetails)) ? lang('update_customer') : lang('add_customer') ?></button>
        </div>
      </div>
    </form>
  </div>
</section>
