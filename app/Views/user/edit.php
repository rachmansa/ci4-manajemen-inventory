<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>
<!-- Content wrapper -->
<div class="content-wrapper">
   <!-- Content -->
   <div class="container-xxl flex-grow-1 container-p-y">
      <!-- Basic Layout & Basic with Icons -->
      <div class="row">
         <!-- Basic Layout -->
         <div class="col-xxl">
            <div class="card mb-4">
               <div class="card-header d-flex align-items-center justify-content-between">
                  <h5 class="mb-0">Edit User</h5>
               </div>
               <div class="card-body">
                  <form action="<?= base_url('user/edit/' . $user['id']) ?>" method="post">
                     <?= csrf_field() ?>
                     <input type="hidden" name="id" value="<?= $user['id'] ?>">
                     <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="username">Username</label>
                        <div class="col-sm-10">
                           <input type="text" class="form-control" name="username" id="username" value="<?= set_value('username', $user['username']) ?>">
                           <?php if (isset($validation) && $validation->hasError('username')) : ?>
                           <div class="text-danger"><?= $validation->getError('username') ?></div>
                           <?php endif; ?>
                        </div>
                     </div>
                     <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="password">Password </label>
                        <div class="col-sm-10">
                           <input type="password" class="form-control" name="password" id="password">
                           <?php if (isset($validation) && $validation->hasError('password')) : ?>
                           <div class="text-danger"><?= $validation->getError('password') ?></div>
                           <?php endif; ?>
                           <div class="form-text">Kosongkan jika tidak ingin mengubah password</div>
                        </div>
                     </div>
                     <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="email">Email</label>
                        <div class="col-sm-10">
                           <div class="input-group input-group-merge">
                              <input type="email" class="form-control" name="email" id="email" value="<?= set_value('email', $user['email']) ?>">
                              <?php if (isset($validation) && $validation->hasError('email')) : ?>
                              <div class="text-danger"><?= $validation->getError('email') ?></div>
                              <?php endif; ?>
                           </div>
                        </div>
                     </div>
                     <div class="row mb-3">
                        <label class="col-sm-2 col-form-label" for="role_id">Role</label>
                        <div class="col-sm-10">
                           <select name="role_id" id="role_id" class="form-select">
                              <option value="1" <?= $user['role_id'] == 1 ? 'selected' : '' ?>>Super Admin</option>
                              <option value="2" <?= $user['role_id'] == 2 ? 'selected' : '' ?>>Admin</option>
                              <option value="3" <?= $user['role_id'] == 3 ? 'selected' : '' ?>>Staff</option>
                           </select>
                        </div>
                     </div>
                     <div class="row justify-content-end">
                        <div class="col-sm-10">
                           <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- / Content -->
   <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>