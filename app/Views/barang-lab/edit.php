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
                  <label for="id_barang_detail" class="form-label">Nama Barang</label>
                  <select name="id_barang_detail" id="id_barang_detail" class="form-select" required>
                     <option value="">-- Pilih Barang --</option>
                     <?php foreach ($available_barang as $barang) : ?>
                     <option value="<?= $barang['id_barang_detail'] ?>" 
                        <?= old('id_barang_detail', $barang_lab['id_barang_detail']) == $barang['id_barang_detail'] ? 'selected' : '' ?>>
                        <?= $barang['nama_barang'] ?> 
                        <?= $barang['serial_number'] ? " - ($barang[serial_number])" : ($barang['nomor_bmn'] ? " - ($barang[nomor_bmn])" : '') ?>
                     </option>
                     <?php endforeach; ?>
                  </select>
               </div>

               <div class="mb-3">
                  <label for="serial_number" class="form-label">Serial Number</label>
                  <input type="text" id="serial_number" class="form-control" 
                     value="<?= $barang_lab['serial_number'] ?>" readonly>
               </div>

               <div class="mb-3">
                  <label for="nomor_bmn" class="form-label">Nomor BMN</label>
                  <input type="text" id="nomor_bmn" class="form-control" 
                     value="<?= $barang_lab['nomor_bmn'] ?>" readonly>
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
                     <option value="Diperbaiki" <?= old('kondisi', $barang_lab['kondisi']) == 'Diperbaiki' ? 'selected' : '' ?>>Diperbaiki</option>
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

<script>
   document.addEventListener("DOMContentLoaded", function () {
       const id_barang_detail = document.getElementById("id_barang_detail");
       const serial_number = document.getElementById("serial_number");
       const nomor_bmn = document.getElementById("nomor_bmn");

       id_barang_detail.addEventListener("change", function () {
           const selectedOption = this.options[this.selectedIndex];
           const text = selectedOption.textContent;
           const matchSN = text.match(/\((.*?)\)/);

           serial_number.value = "";
           nomor_bmn.value = "";

           if (matchSN) {
               const snOrBmn = matchSN[1];
               if (!isNaN(snOrBmn)) {
                   nomor_bmn.value = snOrBmn;
               } else {
                   serial_number.value = snOrBmn;
               }
           }
       });
   });
</script>
