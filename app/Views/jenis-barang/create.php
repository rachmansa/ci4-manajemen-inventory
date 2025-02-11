<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<!-- Content wrapper -->
<div class="content-wrapper">
   <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="fw-bold py-3 mb-4">Tambah Jenis Barang</h4>

      <div class="card">
         <div class="card-header">
            <h5 class="mb-0">Form Tambah Jenis Barang</h5>
         </div>
         <div class="card-body">
            <form action="<?= base_url('jenis-barang/store') ?>" method="post">
               <?= csrf_field() ?>
               <div class="row mb-3">
                  <label class="col-sm-2 col-form-label" for="nama_jenis">Nama Jenis Barang</label>
                  <div class="col-sm-10">
                     <input type="text" class="form-control" name="nama_jenis" id="nama_jenis" value="<?= set_value('nama_jenis') ?>" required>
                     <?php if (isset($validation) && $validation->hasError('nama_jenis')) : ?>
                     <div class="text-danger"><?= $validation->getError('nama_jenis') ?></div>
                     <?php endif; ?>
                  </div>
               </div>
               <div class="row justify-content-end">
                  <div class="col-sm-10">
                     <button type="submit" class="btn btn-primary">Simpan</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>
