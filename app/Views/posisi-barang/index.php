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
            <h5 class="m-0">Daftar Posisi</h5>
            <a href="<?= base_url('posisi-barang/create') ?>" class="btn btn-primary btn-lg">Tambah Posisi</a>
         </div>
         <div class="table-responsive text-nowrap">
            <table id="posisiTable" class="table table-striped">
               <thead>
                  <tr>
                     <th>Nama Posisi</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($posisis as $posisi) : ?>
                  <tr>
                     <td><?= $posisi['nama_posisi'] ?></td>
                     <td>
                        <a href="<?= base_url('posisi-barang/edit/' . $posisi['id_posisi']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form action="<?= base_url('posisi-barang/delete/' . $posisi['id_posisi']) ?>" method="post" style="display:inline;">
                           <?= csrf_field() ?>
                           <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus posisi ini?');">Hapus</button>
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
    $('#posisiTable').DataTable();
});
</script>
