<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Tambah Barang Pegawai</h5>
            </div>

            <div class="card-body">
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('barang-pegawai-unit/store') ?>" method="post">
                    <?= csrf_field() ?>

                    <input type="hidden" name="id_pegawai_unit" value="<?= $pegawai_unit['id_pegawai_unit'] ?>">

                    <div class="row mb-3">
                        <label for="nama_pegawai" class="col-sm-3 col-form-label">Pegawai<span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="<?= $pegawai_unit['nip'] ?> - <?= $pegawai_unit['nama_pegawai'] ?>" readonly>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="jenis_penggunaan" class="col-sm-3 col-form-label">Jenis Penggunaan<span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="jenis_penggunaan" id="jenis_penggunaan" value="Barang Pegawai" readonly>
                        </div>
                    </div>

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
                            <select class="form-select select2" name="id_barang_detail" id="id_barang_detail">
                                <option value="">Pilih Barang Detail</option>
                        </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="kondisi_barang" class="col-sm-3 col-form-label">Kondisi Barang</label>
                        <div class="col-sm-9">
                            <select name="kondisi_barang" id="kondisi_barang" class="form-select" required>
                                <option value="baik" <?= old('kondisi_barang') == 'baik' ? 'selected' : '' ?>>Baik</option>
                                <option value="rusak" <?= old('kondisi_barang') == 'rusak' ? 'selected' : '' ?>>Rusak</option>
                                <option value="hilang" <?= old('kondisi_barang') == 'hilang' ? 'selected' : '' ?>>Hilang</option>
                            </select>
                        </div>
                    </div>                

                    <div class="row mb-3">
                        <label for="tanggal_serah_terima_awal" class="col-sm-3 col-form-label">Tanggal Serah Terima Awal <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" name="tanggal_serah_terima_awal" id="tanggal_serah_terima_awal">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="tanggal_serah_terima_akhir" class="col-sm-3 col-form-label">Tanggal Serah Terima Akhir </label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" name="tanggal_serah_terima_akhir" id="tanggal_serah_terima_akhir">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="keterangan" class="col-sm-3 col-form-label">Keterangan</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" name="keterangan" id="keterangan" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="<?= base_url('barang-pegawai-unit') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>

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
                url: "<?= base_url('barang-pegawai-unit/get-barang-detail') ?>", 
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

});
</script>
