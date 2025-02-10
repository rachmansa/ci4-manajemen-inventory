<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<!-- Content wrapper -->
<div class="content-wrapper">
   <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="fw-bold py-3 mb-4">Tambah Barang Masuk</h4>

      <div class="card">
         <div class="card-header">
            <h5 class="mb-0">Form Tambah Barang Masuk</h5>
         </div>
         <div class="card-body">
            <form action="<?= base_url('barang-masuk/store') ?>" method="post">
               <?= csrf_field() ?>

               <div class="mb-3">
                  <label for="id_barang" class="form-label">Barang</label>
                  <select name="id_barang" id="id_barang" class="form-control" required>
                     <option value="">-- Pilih Barang --</option>
                     <?php foreach ($barang as $b) : ?>
                        <option value="<?= $b['id_barang'] ?>"><?= $b['nama_barang'] ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>

               <div class="mb-3">
                  <label for="id_jenis_penggunaan" class="form-label">Jenis Penggunaan</label>
                  <select name="id_jenis_penggunaan" id="id_jenis_penggunaan" class="form-control" required>
                     <option value="">-- Pilih Jenis Penggunaan --</option>
                     <?php foreach ($jenis_penggunaan as $jp) : ?>
                        <option value="<?= $jp['id_penggunaan'] ?>"><?= $jp['nama_penggunaan'] ?></option>
                     <?php endforeach; ?>
                  </select>
               </div>

               <div class="mb-3">
                  <label for="jumlah" class="form-label">Jumlah</label>
                  <input type="number" class="form-control" name="jumlah" id="jumlah" required>
               </div>

               <div class="mb-3">
                  <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                  <input type="date" class="form-control" name="tanggal_masuk" id="tanggal_masuk" required>
               </div>

               <div class="mb-3">
                  <label for="keterangan" class="form-label">Keterangan</label>
                  <textarea class="form-control" name="keterangan" id="keterangan"></textarea>
               </div>

               <button type="submit" class="btn btn-primary">Simpan</button>
               <a href="<?= base_url('barang-masuk') ?>" class="btn btn-secondary">Kembali</a>
            </form>
         </div>
      </div>
   </div>
</div>

<?= $this->include('layouts/footer') ?>
