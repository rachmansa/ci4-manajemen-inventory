<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>
<div class="content-wrapper">
   <div class="container-xxl flex-grow-1 container-p-y">
      <?php if (session()->getFlashdata('success')) : ?>
      <div class="alert alert-success"> <?= session()->getFlashdata('success') ?> </div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('error')) : ?>
      <div class="alert alert-danger"> <?= session()->getFlashdata('error') ?> </div>
      <?php endif; ?>
      <div class="card">
         <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0">Edit Barang Pegawai</h5>
         </div>
         <div class="card-body">
            <form action="<?= base_url('barang-pegawai-unit/update/' . $barangPegawaiUnit['id_barang_pegawai_unit']) ?>" method="post">
               <?= csrf_field() ?>
               <div class="row mb-3">
                  <label class="col-sm-3 form-label">Pegawai</label>
                  <div class="col-sm-9">
                     <input type="text" class="form-control" 
                              value="<?= isset($pegawai_unit['nip']) && isset($pegawai_unit['nama_pegawai']) 
                                       ? $pegawai_unit['nip'] . ' - ' . $pegawai_unit['nama_pegawai'] 
                                       : 'Data pegawai tidak tersedia' ?>" 
                              readonly>
                     <input type="hidden" name="id_pegawai_unit" value="<?= $pegawai_unit['id_pegawai_unit'] ?? '' ?>">
                  </div>
               </div>

               <div class="row mb-3">
                  <label class="col-sm-3 form-label">Jenis Penggunaan</label>
                  <div class="col-sm-9">
                     <input type="text" class="form-control" value="<?= $jenis_penggunaan['nama_penggunaan']?>" readonly>
                  </div>
               </div>
               <div class="row mb-3">
                  <label class="col-sm-3 form-label">Barang</label>
                  <div class="col-sm-9">
                     <select class="form-select select2" name="id_barang" id="id_barang" required>
                        <option value="">Pilih Barang</option>
                        <?php foreach ($barang as $b) : ?>
                        <?php if ($b['stok'] > 0 || $b['id_barang'] == $barangPegawaiUnit['id_barang']) : ?>
                        <option value="<?= $b['id_barang'] ?>" data-stok="<?= $b['stok'] ?>"
                           <?= $barangPegawaiUnit['id_barang'] == $b['id_barang'] ? 'selected' : '' ?>>
                           <?= esc($b['nama_barang']) ?> (Stok: <?= $b['stok'] ?>)
                        </option>
                        <?php endif; ?>
                        <?php endforeach; ?>
                     </select>
                  </div>
               </div>
               <div class="row mb-3" id="barang_detail_group">
                    <label for="id_barang_detail" class="col-sm-3 col-form-label">Barang Detail <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <select class="form-select select2" name="id_barang_detail" id="id_barang_detail">
                            <option value="">Pilih Barang Detail</option>
                            <?php foreach ($barang_details as $detail) : ?>
                                <option value="<?= $detail['id_barang_detail'] ?>" 
                                        <?= isset($barangPegawaiUnit['id_barang_detail']) && $barangPegawaiUnit['id_barang_detail'] == $detail['id_barang_detail'] ? 'selected' : '' ?>>
                                    <?= $detail['merk'] . ' - ' . 
                                        (!empty($detail['serial_number']) ? $detail['serial_number'] : '(Tidak Ada)') . ' - ' . 
                                        (!empty($detail['nomor_bmn']) ? $detail['nomor_bmn'] : '(Tidak Ada)') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

               <div class="row mb-3">
                  <label class="col-sm-3 form-label">Kondisi Barang</label>
                  <div class="col-sm-9">
                     <select name="kondisi_barang" class="form-select" required>
                        <option value="baik" <?= $barangPegawaiUnit['kondisi_barang'] == 'baik' ? 'selected' : '' ?>>Baik</option>
                        <option value="rusak" <?= $barangPegawaiUnit['kondisi_barang'] == 'rusak' ? 'selected' : '' ?>>Rusak</option>
                        <option value="hilang" <?= $barangPegawaiUnit['kondisi_barang'] == 'hilang' ? 'selected' : '' ?>>Hilang</option>
                     </select>
                  </div>
               </div>
               <div class="row mb-3">
                  <label class="col-sm-3 form-label">Tanggal Serah Terima Awal</label>
                  <div class="col-sm-9">
                     <input type="date" class="form-control" name="tanggal_serah_terima_awal" value="<?= $barangPegawaiUnit['tanggal_serah_terima_awal'] ?>">
                  </div>
               </div>
               <div class="row mb-3">
                  <label class="col-sm-3 form-label">Tanggal Serah Terima Akhir</label>
                  <div class="col-sm-9">
                     <input type="date" class="form-control" name="tanggal_serah_terima_akhir" value="<?= $barangPegawaiUnit['tanggal_serah_terima_akhir'] ?>">
                  </div>
               </div>
               <div class="row mb-3">
                  <label class="col-sm-3 form-label">Keterangan</label>
                  <div class="col-sm-9">
                     <textarea class="form-control" name="keterangan"> <?= esc($barangPegawaiUnit['keterangan']) ?> </textarea>
                  </div>
               </div>
               <div class="text-end">
                  <a href="<?= base_url('barang-pegawai-unit') ?>" class="btn btn-secondary">Batal</a>
                  <button type="submit" class="btn btn-primary" id="btn-simpan">Simpan Perubahan</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>
<script>
   document.getElementById("id_barang").addEventListener("change", function() {
      var selectedBarang = this.options[this.selectedIndex];
      var stok = selectedBarang.getAttribute("data-stok");
      if (stok <= 0) {
         alert("Barang ini tidak memiliki stok. Pilih barang lain.");
         this.value = "";
      }
   });
</script>
