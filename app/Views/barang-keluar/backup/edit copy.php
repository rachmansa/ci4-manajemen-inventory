<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Barang Keluar</h5>
            </div>

            <div class="card-body">
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('barang-keluar/update/' . $barang_keluar['id_barang_keluar']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="row mb-3">
                        <label for="id_barang" class="col-sm-3 col-form-label">Nama Barang</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" value="<?= esc($barang['nama_barang']) ?>" readonly>
                            <input type="hidden" name="id_barang" value="<?= $barang_keluar['id_barang'] ?>">
                        </div>
                    </div>

                    <div class="row mb-3 <?= empty($barang_keluar['id_barang_detail']) ? 'd-none' : '' ?>" id="serial_number_section">
                        <label for="id_barang_detail" class="col-sm-3 col-form-label">Barang Detail</label>
                        <div class="col-sm-9">
                            <select class="form-select" name="id_barang_detail[]" id="id_barang_detail" multiple>
                                <?php foreach ($barang_detail as $detail) : ?>
                                    <option value="<?= $detail['id_barang_detail'] ?>"
                                        <?= in_array($detail['id_barang_detail'], explode(',', $barang_keluar['id_barang_detail'])) ? 'selected' : '' ?>>
                                        <?= esc($detail['serial_number'] ?: 'Tanpa SN') ?> - <?= esc($detail['nomor_bmn'] ?: 'Tanpa BMN') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="jumlah" class="col-sm-3 col-form-label">Jumlah</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" name="jumlah" id="jumlah"
                                value="<?= esc($barang_keluar['jumlah']) ?>"
                                <?= !empty($barang_keluar['id_barang_detail']) ? 'readonly' : '' ?>>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="alasan" class="col-sm-3 col-form-label">Alasan Barang Keluar</label>
                        <div class="col-sm-9">
                            <select class="form-select" id="alasan" name="alasan" required>
                                <option value="">-- Pilih Alasan --</option>
                                <?php foreach ($alasan as $a) : ?>
                                    <option value="<?= $a ?>" <?= $barang_keluar['alasan'] == $a ? 'selected' : '' ?>>
                                        <?= $a ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="pihak_penerima" class="col-sm-3 col-form-label">Penerima Barang</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="pihak_penerima" id="pihak_penerima"
                                value="Biro Umum">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="tanggal_keluar" class="col-sm-3 col-form-label">Tanggal Keluar</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" name="tanggal_keluar" id="tanggal_keluar"
                                value="<?= esc($barang_keluar['tanggal_keluar']) ?>" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="keterangan" class="col-sm-3 col-form-label">Keterangan</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" name="keterangan" id="keterangan" rows="3"><?= esc($barang_keluar['keterangan']) ?></textarea>
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

<!-- Select2 JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
$(function () {
    if (typeof $.fn.select2 === 'undefined') {
        console.error("Select2 belum dimuat. Pastikan urutan jQuery dan Select2 benar.");
        return;
    }

    // Inisialisasi Select2 untuk Barang Detail
    $('#id_barang_detail').select2({
        placeholder: "Pilih Serial Number",
        allowClear: true,
        width: '100%'
    });

    // Cegah duplikasi saat memilih Barang Detail
    $('#id_barang_detail').on('select2:select', function () {
        let selectedValues = $(this).val();

        if (selectedValues) {
            let uniqueValues = [...new Set(selectedValues)]; // Hilangkan duplikat
            if (uniqueValues.length !== selectedValues.length) {
                alert("Barang Detail sudah dipilih sebelumnya!");
                $(this).val(uniqueValues).trigger('change'); // Reset pilihan
            }
        }
    });

    // Saat Barang Detail dipilih/hapus, atur jumlah otomatis
    $('#id_barang_detail').on('change', function () {
        let jumlah = $(this).val() ? $(this).val().length : 0;

        if (jumlah > 0) {
            $('#jumlah').val(jumlah).prop('readonly', true); // Jika ada Barang Detail, readonly
        } else {
            let jumlahSebelumnya = $('#jumlah').data('jumlah-lama'); // Ambil jumlah lama
            $('#jumlah').val(jumlahSebelumnya).prop('readonly', false); // Jika tidak ada, bisa diedit
        }
    });

    // Simpan nilai jumlah lama untuk dipakai kembali jika Barang Detail dikosongkan
    $('#jumlah').data('jumlah-lama', $('#jumlah').val());
});
</script>

