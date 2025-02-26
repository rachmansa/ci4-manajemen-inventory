<?php

namespace App\Models;

use CodeIgniter\Model;

class PeminjamanModel extends Model
{
    protected $table            = 'peminjaman';
    protected $primaryKey       = 'id_peminjaman';
    protected $allowedFields    = [
        'pegawai_id', 'id_barang','id_barang_detail', 'tanggal_peminjaman',
        'tanggal_pengembalian', 'kondisi_awal', 'kondisi_akhir', 'status', 'created_at', 'updated_at'
    ];
    protected $useTimestamps    = true;

    // Ambil semua data peminjaman dengan join ke pegawai & barang
    public function getAllPeminjaman()
    {
        return $this->db->table($this->table)
            ->select('peminjaman.*, pegawai_unit.nama_pegawai, pegawai_unit.unit_kerja, 
                    barang_detail.serial_number, barang_detail.nomor_bmn, barang.nama_barang, barang_detail.kondisi AS kondisi_awal')
            ->join('pegawai_unit', 'pegawai_unit.id_pegawai_unit = peminjaman.pegawai_id')
            ->join('barang_detail', 'barang_detail.id_barang_detail = peminjaman.id_barang_detail')
            ->join('barang', 'barang.id_barang = barang_detail.id_barang')
            ->get()
            ->getResultArray();
    }

    // Proses peminjaman barang
    public function pinjamBarang($pegawai_id, $id_barang_detail)
    {
        $barangDetailModel = new \App\Models\BarangDetailModel();
        $barangModel = new \App\Models\BarangModel();

        $barang = $barangDetailModel->find($id_barang_detail);
        if (!$barang || $barang['status'] !== 'tersedia') {
            return false;
        }
       
        $this->db->transStart(); // Mulai transaksi

        // Simpan peminjaman
        $this->insert([
            'pegawai_id'       => $pegawai_id,
            'id_barang'        => $barang['id_barang'],
            'id_barang_detail' => $id_barang_detail,
            'tanggal_peminjaman' => date('Y-m-d H:i:s'),
            'kondisi_awal'     => $barang['kondisi'],
            'status'           => 'Dipinjam'
        ]);
        // dd([
        //     'pegawai_id'       => $pegawai_id,
        //     'id_barang'        => $barang['id_barang'],
        //     'id_barang_detail' => $id_barang_detail,
        //     'tanggal_peminjaman' => date('Y-m-d H:i:s'),
        //     'kondisi_awal'     => $barang['kondisi'],
        //     'status'           => 'Dipinjam'
        // ]);

        // Update status barang detail menjadi "Terpakai"
        $barangDetailModel->update($id_barang_detail, ['status' => 'terpakai']);
      
        // **Kurangi stok barang utama**
        $barangModel->where('id_barang', $barang['id_barang'])
                    ->set('stok', 'stok - 1', false) 
                    ->update();

        $this->db->transComplete(); // Selesai transaksi

        return $this->db->transStatus();
    }



    // Cek apakah barang bisa dikembalikan
    public function canReturnItem($id_peminjaman)
    {
        $data = $this->find($id_peminjaman);
        return ($data && $data['status'] === 'Dipinjam');
    }

    // Proses pengembalian barang
    // public function kembalikanBarang($id_peminjaman, $kondisi_akhir)
    // {
    //     $barangDetailModel = new \App\Models\BarangDetailModel();
    //     $peminjaman = $this->find($id_peminjaman);

    //     if (!$peminjaman || $peminjaman['status'] !== 'Dipinjam') {
    //         return false;
    //     }

    //     $this->db->transStart(); // Mulai transaksi

    //     // Update status peminjaman
    //     $this->update($id_peminjaman, [
    //         'status'         => 'Dikembalikan',
    //         'tanggal_kembali'=> date('Y-m-d'),
    //         'kondisi_akhir'  => $kondisi_akhir
    //     ]);

    //     // Update status barang kembali ke "Tersedia"
    //     $barangDetailModel->update($peminjaman['id_barang_detail'], ['status' => 'Tersedia', 'kondisi' => $kondisi_akhir]);

    //     $this->db->transComplete(); // Selesai transaksi
    //     return $this->db->transStatus();
    // }

    // Proses pengembalian barang
    public function kembalikanBarang($id_peminjaman, $kondisi_akhir)
    {
        $barangDetailModel = new \App\Models\BarangDetailModel();
        $barangModel = new \App\Models\BarangModel();
        $peminjaman = $this->find($id_peminjaman);

      
        if (!$peminjaman || $peminjaman['status'] !== 'Dipinjam') {
            return false;
        }

        $this->db->transStart(); // Mulai transaksi

        // Update status peminjaman menjadi "Dikembalikan"
        $this->update($id_peminjaman, [
            'tanggal_pengembalian' => date('Y-m-d'),
            'kondisi_akhir'        => $kondisi_akhir,
            'status'               => 'Dikembalikan'
        ]);

        // Update status barang detail menjadi "Tersedia"
        $barangDetailModel->update($peminjaman['id_barang_detail'], ['status' => 'tersedia']);
       
        // Tambah stok barang utama kembali
        $barang = $barangDetailModel->find($peminjaman['id_barang_detail']);
    
        if ($barang) {
            $barangModel->where('id_barang', $barang['id_barang'])
            ->set('stok', 'stok + 1', false)
            ->update();

        }

        $this->db->transComplete(); // Selesai transaksi
        return $this->db->transStatus();
    }

}
