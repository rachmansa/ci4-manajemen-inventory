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
            <h5 class="m-0">Activity Log</h5>
         </div>
         <div class="table-responsive text-nowrap">
            <table class="table">
               <thead>
                  <tr>
                     <th>ID</th>
                     <th>Username</th>
                     <th>Method</th>
                     <th>Endpoint</th>
                     <th>Waktu</th>
                  </tr>
               </thead>
               <tbody class="table-border-bottom-0">
                  <?php foreach ($logs as $log) : ?>
                  <tr>
                     <td><?= $log['id'] ?></td>
                     <td><?= $log['username'] ?? '-' ?></td>
                     <td><?= $log['method'] ?></td>
                     <td><?= $log['endpoint'] ?></td>
                     <td><?= $log['created_at'] ?></td>
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