<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<!-- Content wrapper -->
<div class="content-wrapper">
   <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="fw-bold py-3 mb-4">Edit Jenis Penggunaan</h4>

      <div class="card">
         <div class="card-header">
            <h5 class="mb-0">Form Edit Jenis Penggunaan</h5>
         </div>
         <div class="card-body">
            <form action="<?= base_url('jenis-penggunaan/update/' . $jenispenggunaan['id_penggunaan']) ?>" method="post">
               <?= csrf_field() ?>
               <input type="hidden" name="id_penggunaan" value="<?= $jenispenggunaan['id_penggunaan'] ?>">
               <div class="row mb-3">
                  <label class="col-sm-2 col-form-label" for="nama_penggunaan">Nama Penggunaan</label>
                  <div class="col-sm-10">
                     <input type="text" class="form-control" name="nama_penggunaan" id="nama_penggunaan" value="<?= set_value('nama_penggunaan', $jenispenggunaan['nama_penggunaan']) ?>" required>
                     <?php if (isset($validation) && $validation->hasError('nama_penggunaan')) : ?>
                     <div class="text-danger"><?= $validation->getError('nama_penggunaan') ?></div>
                     <?php endif; ?>
                  </div>
               </div>
               <div class="row justify-content-end">
                  <div class="col-sm-10">
                     <button type="submit" class="btn btn-primary">Update</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<?= $this->include('layouts/footer') ?>
