<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Tambah Barang Keluar</h5>
            </div>

            <div class="card-body">
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('barang-keluar/store') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="row mb-3">
                        <label for="id_barang" class="col-sm-3 col-form-label">Nama Barang <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-select select2" name="id_barang" id="id_barang" required>
                                <option value="">Pilih Barang</option>
                                <?php foreach ($barang as $b) : ?>
                                    <option value="<?= $b['id_barang'] ?>" data-stok="<?= $b['stok'] ?>">
                                        <?= esc($b['nama_barang']) ?> (Stok: <?= $b['stok'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3" id="barang_detail_group">
                        <label for="id_barang_detail" class="col-sm-3 col-form-label">Barang Detail <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-select select2" name="id_barang_detail[]" id="id_barang_detail" multiple>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="jumlah" class="col-sm-3 col-form-label">Jumlah <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="jumlah" id="jumlah" min="1" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="alasan" class="col-sm-3 col-form-label">Alasan Barang Keluar <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-select" id="alasan" name="alasan" required>
                                <option value="">-- Pilih Alasan --</option>
                                <?php foreach ($alasan as $a) : ?>
                                    <option value="<?= $a ?>"><?= $a ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="pihak_penerima" class="col-sm-3 col-form-label">Penerima Barang <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="pihak_penerima" id="pihak_penerima" value="Biro Umum" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="tanggal_keluar" class="col-sm-3 col-form-label">Tanggal Keluar <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" name="tanggal_keluar" id="tanggal_keluar" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="keterangan" class="col-sm-3 col-form-label">Keterangan</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" name="keterangan" id="keterangan" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="<?= base_url('barang-keluar') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHapusLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus barang keluar ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="btnHapus" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>

<!-- Tambahkan Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2();

    $('#id_barang').change(function() {
        var barangId = $(this).val();
        $('#id_barang_detail').empty();
        
        if (barangId) {
            $.ajax({
                url: "<?= base_url('barang-keluar/get-barang-detail') ?>", 
                type: "GET",
                data: { id_barang: barangId },
                success: function(response) {
                    response.forEach(function(detail) {
                        $('#id_barang_detail').append('<option value="' + detail.id_barang_detail + '">' + detail.nama_detail + '</option>');
                    });
                }
            });
        }
    });

    $('#id_barang_detail').change(function() {
        var jumlahDipilih = $(this).val().length;
        $('#jumlah').val(jumlahDipilih);
    });

    $(document).on('click', '.btn-delete', function() {
        var deleteUrl = $(this).data('url');
        $('#btnHapus').attr('href', deleteUrl);
        $('#modalHapus').modal('show');
    });
});
</script>
