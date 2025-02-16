<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="card">
         <div class="card-header">
            <h5>Edit Barang Lab</h5>
         </div>
         <div class="card-body">
            <?php if (session()->has('success')) : ?>
               <div class="alert alert-success"><?= session('success') ?></div>
            <?php endif; ?>

            <?php if (session()->has('error')) : ?>
               <div class="alert alert-danger"><?= session('error') ?></div>
            <?php endif; ?>

            <form action="<?= base_url('barang-lab/update/' . $barang_lab['id_barang_lab']) ?>" method="post">
               <?= csrf_field() ?>

               <div class="mb-3">
                  <label class="form-label">Nama Barang</label>
                  <input type="text" class="form-control" 
                     value="<?= $barang_detail['nama_barang'] ?>" readonly>
               </div>

               <div class="mb-3">
                  <label for="serial_number" class="form-label">Serial Number</label>
                  <input type="text" id="serial_number" class="form-control" 
                     value="<?= $barang_detail['serial_number'] ?>" readonly>
               </div>

               <div class="mb-3">
                  <label for="nomor_bmn" class="form-label">Nomor BMN</label>
                  <input type="text" id="nomor_bmn" class="form-control" 
                     value="<?= $barang_detail['nomor_bmn'] ?>" readonly>
               </div>

               <div class="mb-3">
                  <label for="jumlah" class="form-label">Jumlah</label>
                  <input type="number" name="jumlah" id="jumlah" class="form-control"
                     value="<?= old('jumlah', $barang_lab['jumlah']) ?>"
                     min="1"
                     max="<?= max(1, $stok_tersedia + $barang_lab['jumlah']) ?>"
                     <?= ($barang_detail['serial_number'] || $barang_detail['nomor_bmn']) ? 'readonly' : '' ?>>

                     <small class="text-muted">Maksimal Tambahan: <?= $stok_tersedia ?></small>
               </div>

               

               <div class="mb-3">
                  <label for="id_lab" class="form-label">Lab</label>
                  <select name="id_lab" id="id_lab" class="form-select" required>
                     <option value="">-- Pilih Lab --</option>
                     <?php foreach ($labs as $lab) : ?>
                     <option value="<?= $lab['id_lab'] ?>" 
                        <?= old('id_lab', $barang_lab['id_lab']) == $lab['id_lab'] ? 'selected' : '' ?>>
                        <?= $lab['nama_lab'] ?>
                     </option>
                     <?php endforeach; ?>
                  </select>
               </div>

               <div class="mb-3">
                  <label for="nama_barang_lab" class="form-label">Nama Barang Lab</label>
                  <input type="text" name="nama_barang_lab" id="nama_barang_lab" class="form-control"
                     value="<?= old('nama_barang_lab', $barang_lab['nama_barang_lab']) ?>" required>
               </div>

               <div class="mb-3">
                  <label for="kondisi" class="form-label">Kondisi</label>
                  <select name="kondisi" id="kondisi" class="form-select" required>
                     <option value="Baik" <?= old('kondisi', $barang_lab['kondisi']) == 'Baik' ? 'selected' : '' ?>>Baik</option>
                     <option value="Rusak" <?= old('kondisi', $barang_lab['kondisi']) == 'Rusak' ? 'selected' : '' ?>>Rusak</option>
                     <option value="Hilang" <?= old('kondisi', $barang_lab['kondisi']) == 'Hilang' ? 'selected' : '' ?>>Hilang</option>
                  </select>
               </div>

               <div class="text-end">
                  <a href="<?= base_url('barang-lab') ?>" class="btn btn-secondary">Batal</a>
                  <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>
