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
            <h5 class="m-0">Daftar Jenis Barang</h5>
            <a href="<?= base_url('jenis-barang/create') ?>" class="btn btn-primary btn-lg">Tambah Jenis Barang</a>
         </div>
         <div class="table-responsive text-nowrap">
            <table id="jenisBarangTable" class="table table-striped">
               <thead>
                  <tr>
                     <th>No</th>
                     <th>Nama Jenis Barang</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  <?php 
                  $no = 1;
                  foreach ($jenisbarangs as $jb) : 
                  ?>
                  <tr>
                     <td><?php echo $no++ ;?></td>
                     <td><?= $jb['nama_jenis'] ?></td>
                     <td>
                        <a href="<?= base_url('jenis-barang/edit/' . $jb['id_jenis']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form action="<?= base_url('jenis-barang/delete/' . $jb['id_jenis']) ?>" method="post" style="display:inline;">
                           <?= csrf_field() ?>
                           <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus jenis barang ini?');">Hapus</button>
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

<?= $this->include('layouts/footer') ?>

<!-- Initialize DataTable -->
<script>
$(document).ready(function() {
    $('#jenisBarangTable').DataTable();
});
</script>
