<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>
<div class="content-wrapper">
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="card">
         <div class="card-header">
            <h5>Edit Barang Detail</h5>
         </div>
         <div class="card-body">
            <?php if (session()->has('success')) : ?>
               <div class="alert alert-success"><?= session('success') ?></div>
            <?php endif; ?>

            <?php if (session()->has('error')) : ?>
               <div class="alert alert-danger"><?= session('error') ?></div>
            <?php endif; ?>
            <form action="<?= base_url('barang-detail/update/' . $barang_detail['id_barang_detail']) ?>" method="post">
               <?= csrf_field() ?>
               <input type="hidden" name="id_barang_detail" value="<?= $barang_detail['id_barang_detail'] ?>">
               <div class="mb-3">
                  <label for="id_barang" class="form-label">Nama Barang</label>
                  <select name="id_barang" id="id_barang" class="form-select" disabled required>
                     <option value="">-- Pilih Barang --</option>
                     <?php foreach ($barangs as $barang) : ?>
                     <option value="<?= $barang['id_barang'] ?>" <?= $barang_detail['id_barang'] == $barang['id_barang'] ? 'selected' : '' ?>>
                        <?= $barang['nama_barang'] ?>
                     </option>
                     <?php endforeach; ?>
                  </select>
                  <input type="hidden" name="id_barang" value="<?= $barang_detail['id_barang'] ?>">
               </div>
               <div class="mb-3">
                  <label for="merk" class="form-label">Merk & Tipe Barang</label>
                  <input type="text" name="merk" id="merk" class="form-control" value="<?= $barang_detail['merk'] ?>">
               </div>
               <div class="mb-3">
                  <label for="nomor_bmn" class="form-label">Nomor BMN</label>
                  <input type="text" name="nomor_bmn" id="nomor_bmn" class="form-control" value="<?= $barang_detail['nomor_bmn'] ?>">
               </div>
               <div class="mb-3">
                  <label for="serial_number" class="form-label">Serial Number</label>
                  <input type="text" name="serial_number" id="serial_number" class="form-control" value="<?= $barang_detail['serial_number'] ?>">
                  <small class="form-text text-muted">Opsional, hanya jika barang memiliki serial number.</small>
               </div>
               <div class="mb-3">
                  <label for="tahun_barang" class="form-label">Tahun Barang</label>
                  <select name="tahun_barang" id="tahun_barang" class="form-select" required data-selected="<?= $barang_detail['tahun_barang'] ?>">
                     <!-- Opsi akan diisi oleh JavaScript -->
                  </select>
               </div>
               <div class="mb-3">
                  <label for="id_jenis_penggunaan" class="form-label">Jenis Penggunaan</label>
                  <select name="id_jenis_penggunaan" id="id_jenis_penggunaan" class="form-select" required>
                     <option value="">-- Pilih Jenis Penggunaan --</option>
                     <?php foreach ($jenisPenggunaan as $jenis) : ?>
                     <option value="<?= $jenis['id_penggunaan'] ?>" <?= $barang_detail['id_jenis_penggunaan'] == $jenis['id_penggunaan'] ? 'selected' : '' ?>>
                        <?= $jenis['nama_penggunaan'] ?>
                     </option>
                     <?php endforeach; ?>
                  </select>
               </div>
               <div class="mb-3">
                  <label for="posisi_barang" class="form-label">Posisi Barang</label>
                  <input type="text" name="posisi_barang" id="posisi_barang" class="form-control" value="<?= $barang_detail['posisi_barang'] ?>">
               </div>
               <div class="mb-3">
                  <input type="hidden" name="status" value="<?= $barang_detail['status'] ?>">

                  <label for="status" class="form-label">Status</label>
                  <select name="status" id="status" class="form-select" disabled required>
                     <option value="tersedia" <?= $barang_detail['status'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                     <option value="terpakai" <?= $barang_detail['status'] == 'terpakai' ? 'selected' : '' ?>>Terpakai</option>
                     <option value="dipinjam" <?= $barang_detail['status'] == 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                     <option value="menunggu diperbaiki" <?= $barang_detail['status'] == 'menunggu diperbaiki' ? 'selected' : '' ?>>Menunggu Diperbaiki</option>
                     <option value="hilang" <?= $barang_detail['status'] == 'hilang' ? 'selected' : '' ?>>Hilang</option>
                     <option value="penghapusan aset" <?= $barang_detail['status'] == 'penghapusan aset' ? 'selected' : '' ?>>Penghapusan Aset</option>
                  </select>
               </div>
               <div class="mb-3">
                  <label for="kondisi" class="form-label">Kondisi</label>
                  <select name="kondisi" id="kondisi" class="form-select" required>
                     <option value="baik" <?= $barang_detail['kondisi'] == 'baik' ? 'selected' : '' ?>>Baik</option>
                     <option value="rusak" <?= $barang_detail['kondisi'] == 'rusak' ? 'selected' : '' ?>>Rusak</option>
                     <option value="hilang" <?= $barang_detail['kondisi'] == 'hilang' ? 'selected' : '' ?>>Hilang</option>
                  </select>
               </div>
               <div class="text-end">
                  <a href="<?= base_url('barang-detail') ?>" class="btn btn-secondary">Batal</a>
                  <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
       
       const selectedYear = yearSelect.getAttribute("data-selected");

       let options = '<option value="">-- Pilih Tahun --</option>';
       for (let year = startYear; year <= currentYear; year++) {
           let isSelected = selectedYear == year ? "selected" : "";
           options += `<option value="${year}" ${isSelected}>${year}</option>`;
       }
       yearSelect.innerHTML = options;
   });
</script>

<!-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        var statusSelect = document.getElementById('status');
        if (statusSelect.value === 'terpakai' || statusSelect.value === 'dipinjam') {
            statusSelect.disabled = true;
        }
    });
</script> -->

