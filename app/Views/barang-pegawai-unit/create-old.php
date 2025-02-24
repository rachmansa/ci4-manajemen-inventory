<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5 class="m-0">Tambah Barang Pegawai Unit</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('barang-pegawai-unit/store') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <input type="hidden" name="id_pegawai_unit" value="<?= $pegawai_unit['id_pegawai_unit'] ?>">
                    
                    <div class="mb-3">
                        <label for="nama_pegawai" class="form-label">Pegawai</label>
                        <input type="text" class="form-control" value="<?= $pegawai_unit['nama_pegawai'] ?> (<?= $pegawai_unit['nip'] ?>)" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="id_barang" class="form-label">Nama Barang</label>
                        <select class="form-select" name="id_barang" id="id_barang" required>
                            <option value="">Pilih Barang</option>
                            <?php foreach ($barang as $b) : ?>
                                <option value="<?= $b['id_barang'] ?>" data-stok="<?= $b['stok'] ?>">
                                    <?= $b['nama_barang'] ?> (Stok: <?= $b['stok'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="id_jenis_penggunaan" class="form-label">Jenis Penggunaan</label>
                        <select class="form-select" name="id_jenis_penggunaan" required>
                            <option value="">Pilih Jenis Penggunaan</option>
                            <?php foreach ($jenis_penggunaan as $jp) : ?>
                                <option value="<?= $jp['id_penggunaan'] ?>">
                                    <?= $jp['nama_penggunaan'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" name="jumlah" id="jumlah" min="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal_serah_terima_awal" class="form-label">Tanggal Serah Terima</label>
                        <input type="date" class="form-control" name="tanggal_serah_terima_awal" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= base_url('barang-pegawai-unit') ?>" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>

<script>
$(document).ready(function() {
    $('#id_barang').change(function() {
        var stok = $('option:selected', this).data('stok');
        $('#jumlah').attr('max', stok);
    });
});
</script>
