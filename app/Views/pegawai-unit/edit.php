<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
   <div class="container-xxl flex-grow-1 container-p-y">
      <h4 class="fw-bold py-3 mb-4">Edit Pegawai Unit</h4>

      <div class="card">
         <div class="card-header">
            <h5 class="mb-0">Form Edit Pegawai Unit</h5>
         </div>
         <div class="card-body">
            <form action="<?= base_url('pegawai-unit/update/' . $pegawai_unit['id_pegawai_unit']) ?>" method="post">
               <?= csrf_field() ?>
               <input type="hidden" name="id_pegawai_unit" value="<?= $pegawai_unit['id_pegawai_unit'] ?>">
               <div class="row mb-3">
                  <label class="col-sm-2 col-form-label" for="nip">NIP</label>
                  <div class="col-sm-10">
                     <input type="text" class="form-control" name="nip" id="nip" value="<?= set_value('nip', $pegawai_unit['nip']) ?>">
                     <?php if (isset($validation) && $validation->hasError('nip')) : ?>
                     <div class="text-danger"><?= $validation->getError('nip') ?></div>
                     <?php endif; ?>
                  </div>
               </div>
               <div class="row mb-3">
                  <label class="col-sm-2 col-form-label" for="nama_pegawai">Nama Pegawai</label>
                  <div class="col-sm-10">
                     <input type="text" class="form-control" name="nama_pegawai" id="nama_pegawai" value="<?= set_value('nama_pegawai', $pegawai_unit['nama_pegawai']) ?>">
                     <?php if (isset($validation) && $validation->hasError('nama_pegawai')) : ?>
                     <div class="text-danger"><?= $validation->getError('nama_pegawai') ?></div>
                     <?php endif; ?>
                  </div>
               </div>
               <div class="row mb-3">
                  <label class="col-sm-2 col-form-label" for="unit_kerja">Unit Kerja</label>
                  <div class="col-sm-10">
                     <input type="text" class="form-control" name="unit_kerja" id="unit_kerja" value="<?= set_value('unit_kerja', $pegawai_unit['unit_kerja']) ?>">
                     <?php if (isset($validation) && $validation->hasError('unit_kerja')) : ?>
                     <div class="text-danger"><?= $validation->getError('unit_kerja') ?></div>
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
<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>
