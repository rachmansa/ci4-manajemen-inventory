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
                            <select class="form-select" name="id_barang" id="id_barang" required>
                                <option value="">Pilih Barang</option>
                                <?php foreach ($barang as $b) : ?>
                                    <option value="<?= $b['id_barang'] ?>" data-stok="<?= $b['stok'] ?>">
                                        <?= esc($b['nama_barang']) ?> (Stok: <?= $b['stok'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3 d-none" id="serial_number_section">
                        <label for="id_barang_detail" class="col-sm-3 col-form-label">Barang Detail<span class="text-danger">*</label>
                        <div class="col-sm-9">
                            <select class="form-select" name="id_barang_detail[]" id="id_barang_detail" multiple>
                                <!-- Opsi barang detail akan dimuat dengan AJAX -->
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
                        <label for="alasan" class="col-sm-3 col-form-label">Alasan Barang Keluar<span class="text-danger">*</span></label>
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
                        <label for="pihak_penerima" class="col-sm-3 col-form-label">Penerima Barang<span class="text-danger">*</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" name="pihak_penerima" id="pihak_penerima" value="Biro Umum">
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


<!-- Tambahkan Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

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

    // Saat Barang Detail dipilih (Cegah Duplikasi)
    $('#id_barang_detail').on('select2:select', function () {
        let selectedValues = $(this).val();

        // Jika ada duplikasi, hapus item yang terakhir dipilih
        if (selectedValues) {
            let uniqueValues = [...new Set(selectedValues)]; // Hilangkan duplikat
            if (uniqueValues.length !== selectedValues.length) {
                alert("Barang Detail sudah dipilih sebelumnya!");
                $(this).val(uniqueValues).trigger('change'); // Reset pilihan
            }
        }
    });

    // Saat Barang dipilih, load daftar Barang Detail
    $('#id_barang').change(function () {
        let barangId = $(this).val();
        let serialSelect = $('#id_barang_detail');

        if (!barangId) {
            $('#serial_number_section').addClass('d-none');
            serialSelect.empty().trigger('change'); // Reset Select2
            $('#jumlah').val('').prop('readonly', false); // Bisa input manual
            return;
        }

        // Ambil daftar Barang Detail berdasarkan id_barang
        $.getJSON(`<?= base_url('barang-keluar/get-barang-detail/') ?>${barangId}`)
            .done(function (data) {
                serialSelect.empty();

                if (data.length > 0) {
                    $('#serial_number_section').removeClass('d-none');

                    data.forEach(detail => {
                        let label = `${detail.serial_number ? detail.serial_number : 'Tanpa SN'} - ${detail.nomor_bmn ? detail.nomor_bmn : 'Tanpa BMN'}`;
                        serialSelect.append(new Option(label, detail.id_barang_detail, false, false));
                    });

                    serialSelect.trigger('change');
                } else {
                    $('#serial_number_section').addClass('d-none');
                    serialSelect.empty().trigger('change'); // Reset Select2 jika tidak ada serial number
                    $('#jumlah').val('').prop('readonly', false); // Bisa input manual
                }
            })
            .fail(function () {
                console.error("Gagal mengambil data serial number.");
            });
    });

    // Saat Serial Number dipilih/hapus, atur jumlah otomatis
    $('#id_barang_detail').on('change', function () {
        let jumlah = $(this).val() ? $(this).val().length : 0;

        if (jumlah > 0) {
            $('#jumlah').val(jumlah).prop('readonly', true); // Jika ada serial number, jumlah readonly
        } else {
            $('#jumlah').val('').prop('readonly', false); // Jika tidak ada, bisa input manual
        }
    });
});
</script>

