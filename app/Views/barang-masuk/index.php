<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data Barang Masuk</h5>
                <a href="<?= base_url('barang-masuk/create') ?>" class="btn btn-primary">Tambah Barang Masuk</a>
            </div>

            <div class="card-body">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped" id="barangMasukTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Barang</th>
                                <th>Jenis Penggunaan</th>
                                <th>Jumlah</th>
                                <th>Tanggal Masuk</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($barang_masuk as $bm) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($bm['nama_barang']) ?></td>
                                    <td><?= esc($bm['nama_penggunaan']) ?></td>
                                    <td><?= esc($bm['jumlah']) ?></td>
                                    <td><?= date('d-m-Y', strtotime($bm['tanggal_masuk'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('barang-masuk/edit/' . $bm['id_barang_masuk']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $bm['id_barang_masuk'] ?>" data-nama="<?= $bm['nama_barang'] ?>">Hapus</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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

<script>
    $(document).ready(function () {
        $('#barangMasukTable').DataTable();
        // Handle delete button click
        $('.delete-btn').on('click', function() {
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            $('#deleteItemName').text(nama);
            $('#deleteForm').attr('action', '<?= base_url('barang-masuk/delete/') ?>' + id);
            $('#deleteModal').modal('show');
        });
    });

    
</script>
