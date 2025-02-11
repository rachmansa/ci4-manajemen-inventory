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
            <form action="<?= base_url('barang-detail/store') ?>" method="post">
               <?= csrf_field() ?>

               <div class="mb-3">
                  <label for="id_barang" class="form-label">Nama Barang</label>
                  <select name="id_barang" id="id_barang" class="form-select" required>
                     <option value="">-- Pilih Barang --</option>
                     <?php foreach ($barangs as $barang) : ?>
                     <option value="<?= $barang['id_barang'] ?>"><?= $barang['nama_barang'] ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>

               <div class="mb-3">
                  <label for="serial_number" class="form-label">Serial Number</label>
                  <input type="text" name="serial_number" id="serial_number" class="form-control" required>
               </div>

               <div class="mb-3">
                  <label for="status" class="form-label">Status</label>
                  <select name="status" id="status" class="form-select" required>
                     <option value="tersedia">Tersedia</option>
                     <option value="dipinjam">Dipinjam</option>
                     <option value="rusak">Rusak</option>
                     <option value="hilang">Hilang</option>
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

<?= $this->include('layouts/footer') ?>
