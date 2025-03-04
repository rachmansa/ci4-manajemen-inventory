<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>
<div class="content-wrapper">
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="card">
         <div class="card-header">
            <h5>Tambah Barang Detail</h5>
         </div>
         <div class="card-body">
            <?php if (session()->has('success')) : ?>
               <div class="alert alert-success"><?= session('success') ?></div>
            <?php endif; ?>

            <?php if (session()->has('error')) : ?>
               <div class="alert alert-danger"><?= session('error') ?></div>
            <?php endif; ?>
            <form action="<?= base_url('barang-detail/store') ?>" method="post">
               <?= csrf_field() ?>
               <div class="mb-3">
                  <label for="id_barang" class="form-label">Nama Barang</label>
                  <select name="id_barang" id="id_barang" class="form-select" required>
                     <option value="">-- Pilih Barang --</option>
                     <?php foreach ($barangs as $barang) : ?>
                     <option value="<?= $barang['id_barang'] ?>" <?= old('id_barang') == $barang['id_barang'] ? 'selected' : '' ?>>
                        <?= $barang['nama_barang'] ?>
                     </option>
                     <?php endforeach; ?>
                  </select>
               </div>
               <div class="mb-3">
                  <label for="merk" class="form-label">Merk & Tipe Barang</label>
                  <input type="text" name="merk" id="merk" class="form-control" value="<?= old('merk') ?>" required>
              </div>
               <div class="mb-3">
                  <label for="nomor_bmn" class="form-label">Nomor BMN</label>
                  <input type="text" name="nomor_bmn" id="nomor_bmn" class="form-control" value="<?= old('nomor_bmn') ?>">
              </div>
               <div class="mb-3">
                  <label for="serial_number" class="form-label">Serial Number</label>
                  <input type="text" name="serial_number" id="serial_number" class="form-control" value="<?= old('serial_number') ?>">
                  <!-- <small class="form-text text-muted">Opsional, hanya jika barang memiliki serial number.</small> -->
               </div>
               <div class="mb-3">
               <label for="tahun_barang" class="form-label">Tahun Barang</label>
               <select name="tahun_barang" id="tahun_barang" class="form-select" required>
                  <!-- Opsi akan diisi oleh JavaScript -->
               </select>
               </div>
               <div class="mb-3">
                  <label for="id_jenis_penggunaan" class="form-label">Jenis Penggunaan</label>
                  <select name="id_jenis_penggunaan" id="id_jenis_penggunaan" class="form-select" required>
                     <option value="">-- Pilih Jenis Penggunaan --</option>
                     <?php foreach ($jenis_penggunaan as $jenis) : ?>
                     <option value="<?= $jenis['id_penggunaan'] ?>" <?= old('id_jenis_penggunaan') == $jenis['id_penggunaan'] ? 'selected' : '' ?>>
                        <?= $jenis['nama_penggunaan'] ?>
                     </option>
                     <?php endforeach; ?>
                  </select>
               </div>
               <div class="mb-3">
                  <label for="posisi_barang" class="form-label">Posisi Barang</label>
                  <input type="text" name="posisi_barang" id="posisi_barang" class="form-control" value="<?= old('posisi_barang') ?>">
              </div>
               <div class="mb-3">
                  <label for="status" class="form-label">Status</label>
                  <select name="status" id="status" class="form-select" required>
                     <option value="tersedia" <?= old('status') == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                     <option value="terpakai" <?= old('status') == 'terpakai' ? 'selected' : '' ?>>Terpakai</option>
                     <option value="dipinjam" <?= old('status') == 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                     <option value="menunggu diperbaiki" <?= old('status') == 'diperbaiki' ? 'selected' : '' ?>>Menunggu Diperbaiki</option>
                     <option value="hilang" <?= old('status') == 'hilang' ? 'selected' : '' ?>>Hilang</option>
                     <option value="penghapusan aset" <?= old('status') == 'penghapusan aset' ? 'selected' : '' ?>>Penghapusan Aset</option>
                  </select>
               </div>
               <div class="mb-3">
                  <label for="kondisi" class="form-label">Kondisi</label>
                  <select name="kondisi" id="kondisi" class="form-select" required>
                     <option value="baik" <?= old('kondisi') == 'baik' ? 'selected' : '' ?>>Baik</option>
                     <option value="rusak" <?= old('kondisi') == 'rusak' ? 'selected' : '' ?>>Rusak</option>
                     <option value="hilang" <?= old('kondisi') == 'hilang' ? 'selected' : '' ?>>Hilang</option>
                  </select>
               </div>
               <div class="text-end">
                  <a href="<?= base_url('barang-detail') ?>" class="btn btn-secondary">Batal</a>
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
   document.addEventListener("DOMContentLoaded", function () {
       const startYear = 2010;
       const currentYear = new Date().getFullYear();
       const yearSelect = document.getElementById('tahun_barang');

       // Ambil tahun dari input hidden (jika ada, untuk mode edit)
       const selectedYear = yearSelect.getAttribute("data-selected");

       let options = '<option value="">-- Pilih Tahun --</option>';
       for (let year = startYear; year <= currentYear; year++) {
           let isSelected = selectedYear == year ? "selected" : "";
           options += `<option value="${year}" ${isSelected}>${year}</option>`;
       }
       yearSelect.innerHTML = options;
   });
</script>

