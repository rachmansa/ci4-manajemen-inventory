<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
   <div class="container-xxl flex-grow-1 container-p-y">
      <?php if (session()->getFlashdata('success')) : ?>
      <div class="alert alert-success alert-dismissible show" role="alert">
         <?= session()->getFlashdata('success') ?>
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endif; ?>

      <div class="card">
         <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0">Daftar Barang Lab</h5>
            <a href="<?= base_url('barang-lab/create') ?>" class="btn btn-primary btn-lg">Tambah Barang Lab</a>
         </div>
         <div class="table-responsive text-nowrap">
            <table id="barangLabTable" class="table table-striped">
               <thead>
                  <tr>
                     <th>No</th>
                     <th>Barang</th>
                     <th>Serial Number</th>
                     <th>Lab</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($barang_labs as $key => $barangLab) : ?>
                  <tr>
                     <td><?= $key + 1 ?></td>
                     <td><?= $barangLab['nama_barang'] ?></td>
                     <td><?= $barangLab['serial_number'] ?></td>
                     <td><?= $barangLab['nama_lab'] ?></td>
                     <td>
                        <a href="<?= base_url('barang-lab/edit/' . $barangLab['id_barang_lab']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form action="<?= base_url('barang-lab/delete/' . $barangLab['id_barang_lab']) ?>" method="post" class="d-inline">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</button>
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

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus barang detail <strong id="deleteItemName"></strong>?</p>
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
    $('#barangLabTable').DataTable();
    
    // Handle delete button click
    $('.delete-btn').on('click', function() {
        let id = $(this).data('id');
        let nama = $(this).data('nama');
        $('#deleteItemName').text(nama);
        $('#deleteForm').attr('action', '<?= base_url('barang-lab/delete/') ?>' + id);
        $('#deleteModal').modal('show');
    });
});
</script>