<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
   <div class="container-xxl flex-grow-1 container-p-y">
      <div class="card">
         <div class="card-header">
            <h5>Tambah Barang Lab</h5>
         </div>
         
         <div class="card-body">
         <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger">
               <?= session()->getFlashdata('error') ?>
            </div>
         <?php endif; ?>

            <form action="<?= base_url('barang-lab/store') ?>" method="post">
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
               
               <div id="serial_number_section" class="mb-3 d-none">
                  <label for="id_barang_detail" class="form-label">Pilih Serial Number</label>
                  <select class="form-select" name="id_barang_detail[]" id="id_barang_detail" multiple></select>
               </div>

               <div class="mb-3">
                     <label for="jumlah" class="form-label">Jumlah</label>
                     <input type="number" class="form-control" name="jumlah" id="jumlah">
               </div>
               <p id="stok-info" style="margin-top: 10px; font-weight: bold;">Stok Tersedia: -</p>
               
               <div class="mb-3">
                     <label for="id_lab" class="form-label">Lab</label>
                     <select class="form-select" name="id_lab" required>
                        <option value="">-- Pilih Lab --</option>
                        <?php foreach ($labs as $lab) : ?>
                           <option value="<?= $lab['id_lab'] ?>"><?= $lab['nama_lab'] ?></option>
                        <?php endforeach; ?>
                     </select>
               </div>

               <div class="mb-3">
                  <label for="id_jenis_penggunaan" class="form-label">Jenis Penggunaan</label>
                  <input type="text" class="form-control" name="id_jenis_penggunaan" value="Lab" readonly>
               </div>

               <div class="text-end">
                  <a href="<?= base_url('barang-lab') ?>" class="btn btn-secondary">Batal</a>
                  <button type="submit" class="btn btn-primary">Simpan</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>

<!-- Load jQuery terlebih dahulu -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Load Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>
$(function () {
    // Pastikan Select2 tersedia
    if (typeof $.fn.select2 === 'undefined') {
        console.error("Select2 belum dimuat. Pastikan urutan jQuery dan Select2 benar.");
        return;
    }

    // Inisialisasi Select2
    $('#id_barang_detail').select2({
        placeholder: "Pilih Serial Number",
        allowClear: true,
        width: '100%'
    });

    // Saat Barang dipilih
    $('#id_barang').change(function () {
        let barangId = $(this).val();
        let serialSelect = $('#id_barang_detail');

        if (!barangId) {
            $('#serial_number_section').addClass('d-none');
            serialSelect.empty().trigger('change'); // Reset Select2
            $('#jumlah').val('').prop('readonly', false); // Bisa input manual
            return;
        }

        $.getJSON(`<?= base_url('barang-lab/get-serials/') ?>${barangId}`)
            .done(function (data) {
                serialSelect.empty();

                if (data.length > 0) {
                    $('#serial_number_section').removeClass('d-none');
                    data.forEach(serial => {
                        serialSelect.append(new Option(serial.serial_number, serial.id_barang_detail, false, false));
                    });
                    serialSelect.trigger('change');
                } else {
                    $('#serial_number_section').addClass('d-none');
                    serialSelect.empty().trigger('change'); // Reset Select2 jika tidak ada serial number
                    $('#jumlah').val('').prop('readonly', false); // Bisa input manual
                }
            })
            .fail(function () {
                console.error("Gagal mengambil data serial number.");
            });
    });

    // Saat Serial Number dipilih/dihapus
    $('#id_barang_detail').on('change', function () {
        let jumlah = $(this).val().length;

        if (jumlah > 0) {
            $('#jumlah').val(jumlah).prop('readonly', true); // Jika ada serial number, readonly
        } else {
            $('#jumlah').val('').prop('readonly', false); // Jika tidak ada, bisa input manual
        }
    });
});
</script>

<!-- memunculkan stok barang jika barang yang dipilih tidak memiliki serial number -->
<script>
    $(document).ready(function () {
       $('#stok-info').addClass('d-none'); // Sembunyikan info stok

       $('#id_barang').change(function () {
         let barangId = $(this).val();
         let serialSelect = $('#id_barang_detail');

         if (!barangId) {
            $('#serial_number_section').addClass('d-none');
            $('#stok-info').addClass('d-none');
            serialSelect.empty().trigger('change');
            $('#jumlah').val('').prop('readonly', false);
            return;
         }

         $.ajax({
            url: '<?= base_url('barang-lab/get-barang-info') ?>',
            type: 'POST',
            data: { id_barang: barangId },
            dataType: 'json',
            success: function (response) {
                  serialSelect.empty();
                  $('#stok-info').addClass('d-none'); // Sembunyikan stok info secara default

                  if (response.serialNumbers.length > 0) {
                     // Barang memiliki serial number
                     $('#serial_number_section').removeClass('d-none');
                     response.serialNumbers.forEach(serial => {
                        serialSelect.append(new Option(serial.serial_number, serial.id_barang_detail, false, false));
                     });
                     serialSelect.trigger('change');
                  } else {
                     // Barang tidak memiliki serial number
                     $('#serial_number_section').addClass('d-none');
                     serialSelect.empty().trigger('change');
                     $('#stok-info').removeClass('d-none').text('Stok Tersedia: ' + response.stok);
                  }
            },
            error: function () {
                  $('#stok-info').addClass('d-none').text('Gagal mengambil data barang.');
            }
         });
      });

    });
</script>
