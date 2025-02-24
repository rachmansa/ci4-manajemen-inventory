<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
   <div class="container-xxl flex-grow-1 container-p-y">
      <?php if (session()->getFlashdata('success')) : ?>
      <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
      <?php endif; ?>

      <div class="card">
         <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0">Daftar Pegawai Unit</h5>
         </div>
         <div class="table-responsive text-nowrap">
            <table id="pegawaiUnitTable" class="table table-striped">
               <thead>
                  <tr>
                     <th>NIP</th>
                     <th>Nama Pegawai</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  <?php 
                  foreach ($pegawai_units as $pu) : 
                  ?>
                  <tr>
                     <td><?= $pu['nip'] ?></td>
                     <td><?= $pu['nama_pegawai'] ?></td>
                     <td>
                        <a href="<?= base_url('barang-pegawai-unit/create/' . $pu['id_pegawai_unit']) ?>" class="btn btn-primary btn-sm">Tambah Barang</a>
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#barangModal" data-nip="<?= $pu['nip'] ?>">Lihat Barang</button>
                     </td>
                  </tr>
                  <?php endforeach; ?>  
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>

<!-- Modal untuk Menampilkan Barang -->
<div class="modal fade" id="barangModal" tabindex="-1" aria-labelledby="barangModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="barangModalLabel">Daftar Barang</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <div id="barangList" class="table-responsive">
               <!-- Data barang akan dimuat di sini -->
            </div>
         </div>
      </div>
   </div>
</div>



<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini? Data yang dihapus tidak dapat dikembalikan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>
</div>



<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>

<!-- <script>
   $(document).ready(function() {
    $('#pegawaiUnitTable').DataTable();

    $('#barangModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var nip = button.data('nip');
        

        $.ajax({
            url: '<?= base_url('barang-pegawai-unit/getBarangByNip/') ?>' + nip,
            method: 'GET',
            success: function(response) {
                $('#barangList').html(response);
            },
            error: function() {
                $('#barangList').html('<p class="text-center">Data barang tidak ditemukan.</p>');
            }
        });
    });
});

</script> -->

<script>
    var baseUrl = "<?= base_url() ?>";

    $(document).ready(function() {
        $('#pegawaiUnitTable').DataTable();

        $('#barangModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var nip = button.data('nip');

            $.ajax({
                url: baseUrl + 'barang-pegawai-unit/getBarangByNip/' + nip,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#barangList').html(response.html);
                    } else if (response.status === 'empty') {
                        $('#barangList').html('<p class="text-center">' + response.message + '</p>');
                    } else {
                        $('#barangList').html('<p class="text-center text-danger">' + response.message + '</p>');
                    }
                },
                error: function() {
                    $('#barangList').html('<p class="text-center text-danger">Terjadi kesalahan dalam mengambil data.</p>');
                }
            });
        });

        // Ketika tombol hapus diklik
        $(document).on('click', '.deleteBarang', function() {
            let id = $(this).data('id');

            // Simpan ID barang ke dalam tombol konfirmasi menggunakan attr (bukan data)
            $('#confirmDeleteBtn').attr('data-id', id);

            // Tampilkan modal konfirmasi
            $('#confirmDeleteModal').modal('show');
        });

        // Saat tombol konfirmasi di modal diklik
        $('#confirmDeleteBtn').on('click', function() {
            let id = $(this).attr('data-id'); // Ambil data ID yang disimpan di tombol
            let deleteUrl = baseUrl + "barang-pegawai-unit/delete/" + id;

            $.ajax({
                url: deleteUrl,
                type: "POST", // Gunakan POST jika server tidak mendukung DELETE langsung
                dataType: "json",
                data: { method: 'DELETE' }, // Bisa digunakan untuk simulasi DELETE
                success: function(response) {
                    // $('#confirmDeleteModal').modal('hide');
                    // alert(response.success || "Data berhasil dihapus.");
                    // location.reload();
                    if (response.success) {
                        window.location.href = baseUrl + "barang-pegawai-unit?success=" + encodeURIComponent(response.success);
                    }
                },
                error: function(xhr) {
                    let errorMessage = "Gagal menghapus barang.";
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    console.log(xhr.responseText);
                    alert(errorMessage);
                }
            });
        });
    });
</script>
