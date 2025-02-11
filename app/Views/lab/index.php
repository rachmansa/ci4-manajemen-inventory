<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
   <div class="container-xxl flex-grow-1 container-p-y">
      <?php if (session()->getFlashdata('success')) : ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
      <?php endif; ?>

      <div class="card">
         <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0">Daftar Lab</h5>
            <a href="<?= base_url('lab/create') ?>" class="btn btn-primary btn-lg">Tambah Lab</a>
         </div>
         <div class="table-responsive text-nowrap">
            <table id="labTable" class="table table-striped">
               <thead>
                  <tr>
                     <th>Nama Lab</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($labs as $lab) : ?>
                  <tr>
                     <td><?= $lab['nama_lab'] ?></td>
                     <td>
                        <a href="<?= base_url('lab/edit/' . $lab['id_lab']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form action="<?= base_url('lab/delete/' . $lab['id_lab']) ?>" method="post" style="display:inline;">
                           <?= csrf_field() ?>
                           <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus lab ini?');">Hapus</button>
                        </form>
                     </td>
                  </tr>
                  <?php endforeach; ?>  
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>

<!-- Initialize DataTable -->
<script>
$(document).ready(function() {
    $('#labTable').DataTable();
});
</script>
