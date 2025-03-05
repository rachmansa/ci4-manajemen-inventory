<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<!-- Content wrapper -->
<div class="content-wrapper">
   <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="fw-bold py-3 mb-4">Tambah Barang</h4>

      <div class="card">
         <div class="card-header">
            <h5 class="mb-0">Form Tambah Barang</h5>
         </div>
         <div class="card-body">
            <form action="<?= base_url('barang/store') ?>" method="post">
               <?= csrf_field() ?>

               <div class="mb-3">
                  <label for="nama_barang" class="form-label">Nama Barang</label>
                  <input type="text" class="form-control" name="nama_barang" id="nama_barang" required>
               </div>

               <div class="mb-3">
                  <label for="id_jenis" class="form-label">Jenis Barang</label>
                  <select name="id_jenis" id="id_jenis" class="form-control" required>
                        <option value="">-- Pilih Jenis --</option>
                        <?php foreach ($jenis as $j) : ?>
                           <option value="<?= $j['id_jenis'] ?>"><?= $j['nama_jenis'] ?></option>
                        <?php endforeach; ?>
                  </select>
               </div>

               <div class="mb-3">
                  <label for="id_satuan" class="form-label">Satuan Barang</label>
                  <select name="id_satuan" id="id_satuan" class="form-control" required>
                        <option value="">-- Pilih Satuan --</option>
                        <?php foreach ($satuan as $s) : ?>
                           <option value="<?= $s['id_satuan'] ?>"><?= $s['nama_satuan'] ?></option>
                        <?php endforeach; ?>
                  </select>
               </div>

               <div class="mb-3">
                  <label for="stok" class="form-label">Stok</label>
                  <input type="number" class="form-control" name="stok" id="stok" required>
               </div>

               <div class="mb-3">
                  <label for="stok_minimal" class="form-label">Stok Minimal</label>
                  <input type="number" class="form-control" name="stok_minimal" id="stok_minimal" required>
               </div>

               <div class="mb-3">
                  <label for="kode_barang" class="form-label">Kode Barang</label>
                  <input type="text" class="form-control" name="kode_barang" id="kode_barang">
               </div>

               <div class="mb-3">
                  <label for="deskripsi" class="form-label">Deskripsi</label>
                  <textarea class="form-control" name="deskripsi" id="deskripsi" required></textarea>
               </div>
               <a href="<?= base_url('barang') ?>" class="btn btn-secondary">Kembali</a>
               <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
         </div>
      </div>
   </div>
</div>

<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>

<!-- Script AJAX untuk Generate Kode Barang -->
<script>
   $(document).ready(function() {
      $('#id_jenis').change(function() {
         var idJenis = $(this).val();
         if (idJenis) {
            $.ajax({
               url: "<?= base_url('barang/generate-kode') ?>/" + idJenis,
               type: "GET",
               dataType: "json",
               success: function(response) {
                  if (response.success) {
                     $('#kode_barang').val(response.kode_barang);
                  } else {
                     alert("Gagal mendapatkan kode barang!");
                  }
               },
               error: function() {
                  alert("Terjadi kesalahan dalam mengambil kode barang.");
               }
            });
         } else {
            $('#kode_barang').val('');
         }
      });
   });
</script>
