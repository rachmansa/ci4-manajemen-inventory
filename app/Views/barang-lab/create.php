<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <h5>Tambah Barang Lab</h5>
            </div>
            
            <div class="card-body">
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('barang-lab/store') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="id_barang_detail" class="form-label">Nama Barang</label>
                        <select name="id_barang_detail" id="id_barang_detail" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php foreach ($barang_details as $barang) : ?>
                                <option value="<?= $barang['id_barang_detail'] ?>"
                                    data-serial="<?= $barang['serial_number'] ?>"
                                    data-bmn="<?= $barang['nomor_bmn'] ?>">
                                    <?= $barang['nama_barang'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="serial_number" class="form-label">Serial Number</label>
                        <input type="text" class="form-control" name="serial_number" id="serial_number" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="nomor_bmn" class="form-label">Nomor BMN</label>
                        <input type="text" class="form-control" name="nomor_bmn" id="nomor_bmn" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="jenis_penggunaan" class="form-label">Jenis Penggunaan</label>
                        <input type="text" class="form-control" name="jenis_penggunaan" id="jenis_penggunaan" value="Lab CAT" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="id_lab" class="form-label">Lab</label>
                        <select class="form-select" name="id_lab" required>
                            <option value="">-- Pilih Lab --</option>
                            <?php foreach ($labs as $lab) : ?>
                                <option value="<?= $lab['id_lab'] ?>"><?= $lab['nama_lab'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="nama_barang_lab" class="form-label">Nama Barang Lab</label>
                        <p><small>Format Nama : (Nama Barang)-(Kode/Nomor)</small></p>
                        <input type="text" class="form-control" name="nama_barang_lab" required>
                    </div>

                    <div class="mb-3">
                        <label for="kondisi" class="form-label">Kondisi</label>
                        <select class="form-select" name="kondisi" required>
                            <option value="Baik" <?= old('kondisi') == 'Baik' ? 'selected' : '' ?>>Baik</option>
                            <option value="Rusak" <?= old('kondisi') == 'Rusak' ? 'selected' : '' ?>>Rusak</option>
                            <option value="Diperbaiki" <?= old('kondisi') == 'Diperbaiki' ? 'selected' : '' ?>>Diperbaiki</option>
                        </select>
                    </div>

                    <div class="text-end">
                        <a href="<?= base_url('barang-lab') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>

<script>
    $(document).ready(function () {
        $('#id_barang_detail').change(function () {
            let selectedOption = $(this).find(':selected');
            let serialNumber = selectedOption.data('serial');
            let nomorBMN = selectedOption.data('bmn');

            $('#serial_number').val(serialNumber ? serialNumber : ''); 
            $('#nomor_bmn').val(nomorBMN ? nomorBMN : ''); 
        });
    });
</script>
