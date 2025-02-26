<?= $this->include('layouts/head') ?>


<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Form Peminjaman Barang</h5>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <form action="<?= base_url('peminjaman/store') ?>" method="post" id="formPeminjaman">
                    <?= csrf_field() ?>
                    
                    <div class="row mb-3">
                        <label for="pegawai_id" class="col-sm-3 col-form-label">Nama Pegawai <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-select select2" name="pegawai_id" id="pegawai_id" required>
                                <option value="">Pilih Pegawai</option>
                                <?php foreach ($pegawai as $p) : ?>
                                    <option value="<?= $p['id_pegawai_unit'] ?>">
                                        <?= esc($p['nama_pegawai']) ?> - <?= esc($p['nip']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="id_barang" class="col-sm-3 col-form-label">Nama Barang <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-select select2" name="id_barang" id="id_barang" required>
                                <option value="">Pilih Barang</option>
                                <?php foreach ($barang as $b) : ?>
                                    <option value="<?= $b['id_barang'] ?>" data-stok="<?= $b['stok'] ?>" <?= ($b['stok'] <= 0) ? 'disabled' : '' ?>>
                                        <?= esc($b['nama_barang']) ?> (Stok: <?= $b['stok'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3" id="barang_detail_group">
                        <label for="id_barang_detail" class="col-sm-3 col-form-label">Barang Detail <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-select select2" name="id_barang_detail[]" id="id_barang_detail" multiple required>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" name="tanggal_peminjaman" value="<?= date('Y-m-d H:i:s') ?>">

                    <div class="text-end">
                        <a href="<?= base_url('peminjaman') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
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
<script src="https://unpkg.com/html5-qrcode"></script>

<!-- <script>
$(document).ready(function () {
    $('.select2').select2({ placeholder: "Pilih opsi", allowClear: true });

    // Nonaktifkan barang dengan stok 0
    $('#id_barang option').each(function() {
        var stok = $(this).data('stok');
        if (stok <= 0) {
            $(this).prop('disabled', true);
        }
    });

    // Ambil detail barang berdasarkan barang yang dipilih
    $('#id_barang').change(function() {
        var barangId = $(this).val();
        $('#id_barang_detail').empty();

        if (barangId) {
            $.ajax({
                url: "<?= base_url('peminjaman/get-barang-detail') ?>", 
                type: "GET",
                data: { 
                    id_barang: barangId,
                    "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                },
                success: function(response) {
                    $('#id_barang_detail').empty();
                    if (response.length === 0) {
                        $('#id_barang_detail').append('<option disabled>Tidak ada detail barang tersedia</option>');
                    } else {
                        response.forEach(function(detail) {
                            $('#id_barang_detail').append('<option value="' + detail.id_barang_detail + '">' + detail.nama_detail + '</option>');
                        });
                    }
                }
            });
        }
    });

    // Inisialisasi scanner barcode
    let scanner = new Html5Qrcode("barcodeScanner");
    let scanBarcodeInput = document.getElementById("scan_barcode");
    let barangSelect = document.getElementById("id_barang_detail");

    function onScanSuccess(decodedText) {
        scanBarcodeInput.value = decodedText;
        scanner.stop();

        let found = false;
        $("#id_barang_detail option").each(function () {
            if ($(this).text().includes(decodedText)) {
                $(this).prop("selected", true).trigger("change");
                found = true;
            }
        });

        if (!found) {
            alert("Barang dengan barcode ini tidak ditemukan!");
        }
    }

    scanner.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, onScanSuccess);

    // Cegah submit jika tidak memilih barang detail
    $('#formPeminjaman').submit(function(e) {
        var selectedDetails = $('#id_barang_detail').val();
        if (!selectedDetails || selectedDetails.length === 0) {
            alert("Harap pilih minimal satu barang detail.");
            e.preventDefault();
        }
    });
});
</script> -->

<script>
$(document).ready(function () {
    $('.select2').select2({ placeholder: "Pilih opsi", allowClear: true });

    // Nonaktifkan barang dengan stok 0
    $('#id_barang option').each(function() {
        var stok = $(this).data('stok');
        if (stok <= 0) {
            $(this).prop('disabled', true);
        }
    });

    

    // Ambil detail barang berdasarkan barang yang dipilih
    $('#id_barang').change(function() {
        var barangId = $(this).val();
        $('#id_barang_detail').empty().trigger('change'); // Reset barang detail

        if (barangId) {
            $.ajax({
                url: "<?= base_url('peminjaman/get-barang-detail') ?>", 
                type: "GET",
                data: { 
                    id_barang: barangId,
                    "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                },
                success: function(response) {
                    $('#id_barang_detail').empty();
                    if (response.length === 0) {
                        $('#id_barang_detail').append('<option disabled>Tidak ada detail barang tersedia</option>');
                    } else {
                        response.forEach(function(detail) {
                            
                                $('#id_barang_detail').append('<option value="' + detail.id_barang_detail + '">' + detail.nama_detail + '</option>');
                            
                        });
                    }
                }
            });
        }
    });

    // Cegah double submit
    $('#formPeminjaman').submit(function(e) {
        var selectedDetails = $('#id_barang_detail').val();
        if (!selectedDetails || selectedDetails.length === 0) {
            alert("Harap pilih minimal satu barang detail.");
            e.preventDefault();
        } else {
            $(this).find('button[type="submit"]').prop('disabled', true).text('Mengajukan...');
        }
    });

    // Inisialisasi scanner barcode hanya jika elemen tersedia
    if ($("#scan_barcode").length > 0) {
        let scanner = new Html5Qrcode("barcodeScanner");

        function onScanSuccess(decodedText) {
            $("#scan_barcode").val(decodedText);
            scanner.stop();

            let found = false;
            $("#id_barang_detail option").each(function () {
                if ($(this).text().includes(decodedText)) {
                    $(this).prop("selected", true).trigger("change");
                    found = true;
                }
            });

            if (!found) {
                alert("Barang dengan barcode ini tidak ditemukan!");
            }
        }

        scanner.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, onScanSuccess);
    }
});
</script>
