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
            <h5 class="m-0">Daftar Jenis Penggunaan Barang</h5>
            <a href="<?= base_url('jenis-penggunaan/create') ?>" class="btn btn-primary btn-lg">Tambah Jenis Penggunaan</a>
         </div>
         <div class="table-responsive text-nowrap">
            <table id="jenisPenggunaanTable" class="table table-striped">
               <thead>
                  <tr>
                     <th>No</th>
                     <th>Nama Jenis Penggunaan</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  <?php 
                  $no = 1;
                  foreach ($jenispenggunaans as $jp) : 
                  ?>
                  <tr>
                     <td><?php echo $no++ ; ?></td>
                     <td><?= $jp['nama_penggunaan'] ?></td>
                     <td>
                        <a href="<?= base_url('jenis-penggunaan/edit/' . $jp['id_penggunaan']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form action="<?= base_url('jenis-penggunaan/delete/' . $jp['id_penggunaan']) ?>" method="post" style="display:inline;">
                           <?= csrf_field() ?>
                           <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus jenis penggunaan ini?');">Hapus</button>
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
    $('#jenisPenggunaanTable').DataTable();
});
</script>
