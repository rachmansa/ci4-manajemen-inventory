<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <h5>Edit Barang Masuk</h5>
            </div>

            <div class="card-body">
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('barang-masuk/update/' . $barang_masuk['id_barang_masuk']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="id_barang" class="form-label">Nama Barang</label>
                        <select name="id_barang" id="id_barang" class="form-select" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php foreach ($barang as $b) : ?>
                                <option value="<?= $b['id_barang'] ?>" <?= $barang_masuk['id_barang'] == $b['id_barang'] ? 'selected' : '' ?>>
                                    <?= esc($b['nama_barang']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="id_jenis_penggunaan" class="form-label">Jenis Penggunaan</label>
                        <select name="id_jenis_penggunaan" id="id_jenis_penggunaan" class="form-select" required>
                            <option value="">-- Pilih Jenis Penggunaan --</option>
                            <?php foreach ($jenis_penggunaan as $jp) : ?>
                                <option value="<?= $jp['id_penggunaan'] ?>" <?= $barang_masuk['id_jenis_penggunaan'] == $jp['id_penggunaan'] ? 'selected' : '' ?>>
                                    <?= esc($jp['nama_penggunaan']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" name="jumlah" id="jumlah" min="1" value="<?= esc($barang_masuk['jumlah']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                        <input type="date" class="form-control" name="tanggal_masuk" id="tanggal_masuk" value="<?= esc($barang_masuk['tanggal_masuk']) ?>" required>
                    </div>

                    <div class="text-end">
                        <a href="<?= base_url('barang-masuk') ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>
