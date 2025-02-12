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
            <form action="<?= base_url('barang-detail/update/' . $barang_detail['id_barang_detail']) ?>" method="post">
               <?= csrf_field() ?>

               <div class="mb-3">
                  <label for="id_barang" class="form-label">Nama Barang</label>
                  <select name="id_barang" id="id_barang" class="form-select" required>
                     <option value="">-- Pilih Barang --</option>
                     <?php foreach ($barangs as $barang) : ?>
                     <option value="<?= $barang['id_barang'] ?>" <?= $barang['id_barang'] == $barang_detail['id_barang'] ? 'selected' : '' ?>>
                        <?= $barang['nama_barang'] ?>
                     </option>
                     <?php endforeach; ?>
                  </select>
               </div>

               <div class="mb-3">
                  <label for="serial_number" class="form-label">Serial Number (Opsional)</label>
                  <input type="text" name="serial_number" id="serial_number" class="form-control" value="<?= $barang_detail['serial_number'] ?>" required>
               </div>

               <div class="mb-3">
                  <label for="status" class="form-label">Status</label>
                  <select name="status" id="status" class="form-select" required>
                     <option value="tersedia" <?= $barang_detail['status'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                     <option value="dipinjam" <?= $barang_detail['status'] == 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                     <option value="rusak" <?= $barang_detail['status'] == 'rusak' ? 'selected' : '' ?>>Rusak</option>
                     <option value="hilang" <?= $barang_detail['status'] == 'hilang' ? 'selected' : '' ?>>Hilang</option>
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
