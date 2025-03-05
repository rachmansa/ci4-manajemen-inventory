<?= $this->include('layouts/head') ?>
<?= $this->include('layouts/sidebar') ?>
<?= $this->include('layouts/navbar') ?>

<div class="content-wrapper">
   <div class="container-xxl flex-grow-1 container-p-y">
      <?php if (session()->getFlashdata('success')) : ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
         <?= session()->getFlashdata('success') ?>
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php endif; ?>

      <div class="card">
         <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="m-0">Daftar Barang</h5>
            <a href="<?= base_url('barang/create') ?>" class="btn btn-primary btn-lg">Tambah Barang</a>
         </div>
         <div class="table-responsive text-nowrap">
            <table id="barangTable" class="table table-striped">
            <thead>
                  <tr>
                     <th>Nama Barang</th>
                     <th>Kode Barang</th>
                     <th>Jenis Barang</th>
                     <th>Stok</th>
                     <th>Aksi</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($barangs as $barang) : ?>
                  <tr>
                     <td><?= $barang['nama_barang'] ?></td>
                     <td>
                        <?php
                           if (isset($barang['kode_barang']) && !empty($barang['kode_barang'])) {
                              echo $barang['kode_barang'];
                           } else {
                              echo "-";
                           }
                           ?>
                     </td>
                     <td><?= $barang['nama_jenis'] ?></td>
                     <td>
                        Stok Awal : <?= $barang['stok_awal'].' '.$barang['nama_satuan']  ?>  
                        <br>
                        Stok Sekarang : <?= $barang['stok'].' '.$barang['nama_satuan']  ?>  
                        <br>
                        <span style="font-size: 10px;">Minimal : <?= $barang['stok_minimal']?></span>
                     </td>
                     <td>
                        <a href="<?= base_url('barang/edit/' . $barang['id_barang']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <?php if ($barang['total_detail'] > 0) : ?>
                        <button class="btn btn-info btn-sm btn-detail" data-id="<?= $barang['id_barang'] ?>">
                            Lihat Detail
                        </button>
                    <?php endif; ?>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="<?= $barang['id_barang'] ?>" data-nama="<?= $barang['nama_barang'] ?>">Hapus</button>
                     </td>
                  </tr>
                  <?php endforeach; ?>  
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>

<!-- Modal Barang Detail -->
<div class="modal fade" id="modalBarangDetail" tabindex="-1" aria-labelledby="modalBarangDetailLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalBarangDetailLabel">Detail Barang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="table-responsive">
      <div class="modal-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Serial Number</th>
              <th>Nomor BMN</th>
              <th>Posisi</th>
              <th>Jenis Penggunaan</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="barangDetailContent">
            <tr><td colspan="5" class="text-center">Loading...</td></tr>
          </tbody>
        </table>
      </div>
      </div>
      
    </div>
  </div>
</div>


<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus barang <strong id="deleteItemName"></strong>?</p>
         </div>
         <div class="modal-footer">
            <form id="deleteForm" method="post">
               <?= csrf_field() ?>
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
               <button type="submit" class="btn btn-danger">Hapus</button>
            </form>
         </div>
      </div>
   </div>
</div>

<?= $this->include('layouts/wrapper') ?>
<?= $this->include('layouts/footer') ?>

<!-- Initialize DataTable & Modal Handling -->
<script>
$(document).ready(function() {
    $('#barangTable').DataTable();
    
    // Handle delete button click
    $('.delete-btn').on('click', function() {
        let id = $(this).data('id');
        let nama = $(this).data('nama');
        $('#deleteItemName').text(nama);
        $('#deleteForm').attr('action', '<?= base_url('barang/delete/') ?>' + id);
        $('#deleteModal').modal('show');
    });
});
</script>

<script>
$(document).ready(function () {
    $(".btn-detail").click(function () {
        let id_barang = $(this).data("id");

        $("#barangDetailContent").html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');

        $.ajax({
            url: "<?= base_url('barang/detail/'); ?>" + id_barang,
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    let rows = "";
                    response.data.forEach(function (item) {
                        rows += `
                            <tr>
                                <td>${item.serial_number || '-'}</td>
                                <td>${item.nomor_bmn || '-'}</td>
                                <td>${item.posisi_barang}</td>
                                <td>${item.nama_penggunaan}</td>
                                <td>${item.status}</td>
                            </tr>
                        `;
                    });

                    $("#barangDetailContent").html(rows);
                } else {
                    $("#barangDetailContent").html('<tr><td colspan="5" class="text-center">Tidak ada data</td></tr>');
                }
            },
            error: function () {
                $("#barangDetailContent").html('<tr><td colspan="5" class="text-center text-danger">Gagal memuat data</td></tr>');
            }
        });

        $("#modalBarangDetail").modal("show");
    });
});
</script>

<style>
  #modalBarangDetail .modal-dialog {
    max-width: 80%; /* Modal lebih lebar */
  }

  #modalBarangDetail .modal-body {
    max-height: 400px; /* Batasi tinggi modal */
    overflow-y: auto;  /* Scroll jika isi terlalu panjang */
  }
</style>



