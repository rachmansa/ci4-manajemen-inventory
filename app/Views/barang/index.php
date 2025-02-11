<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
   <div class="container-xxl flex-grow-1 container-p-y">
      <?php if (session()->getFlashdata('success')) : ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
         <?= session()->getFlashdata('success') ?>
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endif; ?>

      <div class="card">
         <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0">Daftar Barang</h5>
            <a href="<?= base_url('barang/create') ?>" class="btn btn-primary btn-lg">Tambah Barang</a>
         </div>
         <div class="table-responsive text-nowrap">
            <table id="barangTable" class="table table-striped">
            <thead>
                  <tr>
                     <th>Nama Barang</th>
                     <th>Kode Barang</th>
                     <th>Jenis Barang</th>
                     <th>Stok</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($barangs as $barang) : ?>
                  <tr>
                     <td><?= $barang['nama_barang'] ?></td>
                     <td>
                        <?php
                           if (isset($barang['kode_barang']) && !empty($barang['kode_barang'])) {
                              echo $barang['kode_barang'];
                           } else {
                              echo "-";
                           }
                           ?>
                     </td>
                     <td><?= $barang['nama_jenis'] ?></td>
                     <td>
                        <?= $barang['stok'].' '.$barang['nama_satuan']  ?>  
                        <br>
                        <span style="font-size: 10px;">Minimal : <?= $barang['stok_minimal']?></span>
                     </td>
                     <td>
                        <a href="<?= base_url('barang/edit/' . $barang['id_barang']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $barang['id_barang'] ?>" data-nama="<?= $barang['nama_barang'] ?>">Hapus</button>
                     </td>
                  </tr>
                  <?php endforeach; ?>  
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus barang <strong id="deleteItemName"></strong>?</p>
         </div>
         <div class="modal-footer">
            <form id="deleteForm" method="post">
               <?= csrf_field() ?>
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
               <button type="submit" class="btn btn-danger">Hapus</button>
            </form>
         </div>
      </div>
   </div>
</div>

<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>

<!-- Initialize DataTable & Modal Handling -->
<script>
$(document).ready(function() {
    $('#barangTable').DataTable();
    
    // Handle delete button click
    $('.delete-btn').on('click', function() {
        let id = $(this).data('id');
        let nama = $(this).data('nama');
        $('#deleteItemName').text(nama);
        $('#deleteForm').attr('action', '<?= base_url('barang/delete/') ?>' + id);
        $('#deleteModal').modal('show');
    });
});
</script>