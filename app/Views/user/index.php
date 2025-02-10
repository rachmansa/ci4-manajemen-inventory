<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>
<!-- Content wrapper -->
<div class="content-wrapper">
   <!-- Content -->
   <div class="container-xxl flex-grow-1 container-p-y">
      <!-- Basic Bootstrap Table -->
      <?php if (session()->getFlashdata('success')) : ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
      <?php endif; ?>
      <div class="card">
         <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0">Daftar User</h5>
            <a href="<?= base_url('user/create') ?>" class="btn btn-primary btn-lg">Tambah User</a>
         </div>
         <div class="table-responsive text-nowrap">
            <table class="table">
               <thead>
                  <tr>
                     <th>NO</th>
                     <th>Username</th>
                     <th>Email</th>
                     <th>Role</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody class="table-border-bottom-0">
                  <?php 
                  $no = 1;
                  foreach ($users as $user) : 
                  ?>
                  <tr>
                     <td><?php echo $no++ ; ?></td>
                     <td><?= $user['username'] ?></td>
                     <td><?= $user['email'] ?></td>
                     <td><?= $user['role_id'] == 1 ? 'Super Admin' : ($user['role_id'] == 2 ? 'Admin' : 'Staff') ?></td>
                     <td>
                        <a href="<?= base_url('user/edit/' . $user['id']) ?>" class="btn btn-warning">Edit</a>
                        <form action="<?= base_url('user/delete/' . $user['id']) ?>" method="post" style="display:inline;">
                           <?= csrf_field() ?>
                           <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?');">Hapus</button>
                        </form>
                     </td>
                  </tr>
                  <?php endforeach; ?>  
               </tbody>
            </table>
         </div>
      </div>
      <!--/ Basic Bootstrap Table -->
   </div>
   <!-- / Content -->
</div>
<!-- Content wrapper -->
<?= $this->include('layouts/footer') ?>