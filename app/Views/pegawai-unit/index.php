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
            <h5 class="m-0">Daftar Pegawai Unit</h5>
            <a href="<?= base_url('pegawai-unit/create') ?>" class="btn btn-primary btn-lg">Tambah Pegawai Unit</a>
         </div>
         <div class="table-responsive text-nowrap">
            <table id="pegawaiUnitTable" class="table table-striped">
               <thead>
                  <tr>
                     <th>NO</th>
                     <th>NIP</th>
                     <th>Nama Pegawai</th>
                     <th>Unit Kerja</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  <?php 
                  $no = 1;
                  foreach ($pegawai_units as $pu) : 
                  ?>
                  <tr>
                     <td><?php echo $no++ ;?></td>
                     <td><?= $pu['nip'] ?></td>
                     <td><?= $pu['nama_pegawai'] ?></td>
                     <td><?= $pu['unit_kerja'] ?></td>
                     <td>
                        <a href="<?= base_url('pegawai-unit/edit/' . $pu['id_pegawai_unit']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form action="<?= base_url('pegawai-unit/delete/' . $pu['id_pegawai_unit']) ?>" method="post" style="display:inline;">
                           <?= csrf_field() ?>
                           <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pegawai unit ini?');">Hapus</button>
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
    $('#pegawaiUnitTable').DataTable();
});
</script>
