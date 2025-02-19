<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Data Barang Keluar</h5>
                <a href="<?= base_url('barang-keluar/create') ?>" class="btn btn-primary">Tambah Barang Keluar</a>
            </div>

            <div class="card-body">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped" id="barangKeluarTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Barang</th>
                                <th>Jumlah</th>
                                <th>Barang Detail</th>
                                <th>Alasan</th>
                                <th>Penerima</th>
                                <th>Tanggal Keluar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($barang_keluar as $bk) : ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= esc($bk['nama_barang']) ?></td>
                                    <td><?= esc($bk['jumlah']) ?></td>
                                    <td>
                                        <?php 
                                        $barangDetails = json_decode($bk['barang_details'], true);
                                        
                                        if (!empty($barangDetails)) :
                                            foreach ($barangDetails as $detail) :
                                        ?>
                                            <div>
                                                <ul>
                                                    <li style="list-style-type: none;"><strong><?= esc($detail['merk'] ?: '-') ?></strong></li>
                                                    <li>Serial Number: <?= esc($detail['serial_number'] ?: '-') ?></li>
                                                    <li>BMN: <?= esc($detail['nomor_bmn'] ?: '-') ?></li>
                                                </ul>
                                            </div>
                                            <br>
                                        <?php 
                                            endforeach;
                                        else:
                                            echo "-"; // Jika tidak ada detail barang
                                        endif;
                                        ?>
                                    </td>

                                    <td><?= esc($bk['alasan']) ?></td>
                                    <td><?= esc($bk['pihak_penerima'] ?? '-') ?></td>
                                    <td><?= date('d-m-Y', strtotime($bk['tanggal_keluar'])) ?></td>
                                    <td>
                                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $bk['id_barang_keluar'] ?>" data-nama="<?= $bk['nama_barang'] ?>">Hapus</button>
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
        $('#barangKeluarTable').DataTable();

        // Handle delete button click
        $('.delete-btn').on('click', function() {
            let id = $(this).data('id');
            let nama = $(this).data('nama');
            $('#deleteItemName').text(nama);
            $('#deleteForm').attr('action', '<?= base_url('barang-keluar/delete/') ?>' + id);
            $('#deleteModal').modal('show');
        });
    });
</script>
