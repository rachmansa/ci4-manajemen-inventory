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
      <?php elseif (session()->getFlashdata('error')) : ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
         <?= session()->getFlashdata('error') ?>
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endif; ?>

      <div class="card">
         <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0">Daftar Peminjaman Barang</h5>
            <a href="<?= base_url('peminjaman/create') ?>" class="btn btn-primary btn-lg">Tambah Peminjaman</a>
         </div>
         <div class="table-responsive text-nowrap">
            <table id="peminjamanTable" class="table table-striped">
               <thead>
                  <tr>
                     <th>#</th>
                     <th>Nama Pegawai</th>
                     <th>Barang</th>
                     <th>Serial Number</th>
                     <th>Tanggal Pinjam</th>
                     <th>Status</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  <?php $i = 1; foreach ($peminjaman as $p) : ?>
                  <tr>
                     <td><?= $i++ ?></td>
                     <td><?= esc($p['nama_pegawai']) ?></td>
                     <td><?= esc($p['nama_barang']) ?></td>
                     <td><?= esc($p['serial_number'] ?? '-') ?></td>
                     <td><?= date('d-m-Y H:i', strtotime($p['tanggal_peminjaman'])) ?></td>
                     <td>
                        <span class="badge bg-<?= $p['status'] === 'Dipinjam' ? 'warning' : 'success' ?>">
                            <?= esc($p['status']) ?>
                        </span>
                     </td>
                     <td>
                     <?php if ($p['status'] === 'Dipinjam') : ?>
                        <form action="<?= base_url('/peminjaman/return/' . $p['id_peminjaman']) ?>" method="post" class="d-inline">
                           <?= csrf_field() ?>
                           <button type="button" class="btn btn-success btn-sm return-btn" 
                                 data-id="<?= $p['id_peminjaman'] ?>" 
                                 data-nama="<?= esc($p['nama_barang']) ?>">
                              Kembalikan
                           </button>

                        </form>
                     <?php endif; ?>

                        <button class="btn btn-danger btn-sm delete-btn" 
                                data-id="<?= $p['id_peminjaman'] ?>" 
                                data-nama="<?= esc($p['nama_barang']) ?>">
                            Hapus
                        </button>
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
            <p>Apakah Anda yakin ingin menghapus peminjaman barang <strong id="deleteItemName"></strong>?</p>
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

<!-- Modal Konfirmasi Pengembalian -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnModalLabel">Konfirmasi Pengembalian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="returnForm" method="post">
                <div class="modal-body">
                    <p>Apakah barang <strong><span id="returnItemName"></span></strong> sudah dikembalikan?</p>
                    <input type="hidden" name="id_peminjaman" id="returnItemId">
                    <div class="mb-3">
                        <label for="kondisi_akhir" class="form-label">Kondisi Barang</label>
                        <select name="kondisi_akhir" id="kondisi_akhir" class="form-control" required>
                            <option value="baik">Baik</option>
                            <option value="rusak">Rusak</option>
                            <option value="hilang">Hilang</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Kembalikan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>

<!-- Initialize DataTable & Modal Handling -->

<script>
$(document).ready(function() {
    $('#peminjamanTable').DataTable();

    // Handle delete button click
    $('.delete-btn').on('click', function() {
        let id = $(this).data('id');
        let nama = $(this).data('nama');
        $('#deleteItemName').text(nama);
        $('#deleteForm').attr('action', '<?= base_url('peminjaman/delete/') ?>' + id);
        $('#deleteModal').modal('show');
    });

  
});
</script>

<script>
$(document).ready(function() {
    $('.return-btn').on('click', function() {
        let id = $(this).data('id');
        let nama = $(this).data('nama');
        
        $('#returnItemName').text(nama);
        $('#returnItemId').val(id);
        $('#returnForm').attr('action', '<?= base_url('/peminjaman/return/') ?>' + id);
        
        $('#returnModal').modal('show');
    });
});
</script>


