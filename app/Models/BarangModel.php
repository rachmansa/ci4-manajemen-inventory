<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table            = 'barang';
    protected $primaryKey       = 'id_barang';
    protected $allowedFields    = [
        'nama_barang', 'stok_awal','stok', 'stok_minimal', 'kode_barang',
        'deskripsi', 'id_satuan', 'id_jenis'
    ];

    // Helper untuk mengambil data barang dengan join ke satuan_barang & jenis_barang
    // public function getBarangWithRelations()
    // {
    //     return $this->select('barang.*, satuan_barang.nama_satuan, jenis_barang.nama_jenis')
    //                 ->join('satuan_barang', 'satuan_barang.id_satuan = barang.id_satuan')
    //                 ->join('jenis_barang', 'jenis_barang.id_jenis = barang.id_jenis')
    //                 ->findAll();
    // }

    public function getBarangWithRelations()
    {
        return $this->select('barang.*, satuan_barang.nama_satuan, jenis_barang.nama_jenis, COUNT(barang_detail.id_barang_detail) AS total_detail')
            ->join('satuan_barang', 'satuan_barang.id_satuan = barang.id_satuan')
            ->join('jenis_barang', 'jenis_barang.id_jenis = barang.id_jenis')
            ->join('barang_detail', 'barang_detail.id_barang = barang.id_barang', 'left')
            ->groupBy('barang.id_barang')
            ->findAll();
    }


    // public function getAvailableBarangForDetail()
    // {
    //     return $this->select('barang.*')
    //         ->join('barang_detail', 'barang_detail.id_barang = barang.id_barang', 'left')
    //         ->groupBy('barang.id_barang')
    //         ->having('barang.stok > COUNT(barang_detail.id_barang)', null, false)
    //         ->findAll();
    // }

    public function getAvailableBarangForDetail()
    {
        return $this->select('barang.*')
            ->having('barang.stok > 0', null, false)
            ->findAll();
    }

    public function getBarangDetail($id_barang)
    {
        return $this->db->table('barang_detail')
            ->select('barang_detail.*, barang.nama_barang, satuan_barang.nama_satuan, jenis_barang.nama_jenis, jenis_penggunaan.nama_penggunaan')
            ->join('barang', 'barang.id_barang = barang_detail.id_barang')
            ->join('satuan_barang', 'satuan_barang.id_satuan = barang.id_satuan', 'left')
            ->join('jenis_barang', 'jenis_barang.id_jenis = barang.id_jenis', 'left')
            ->join('jenis_penggunaan', 'jenis_penggunaan.id_penggunaan = barang_detail.id_jenis_penggunaan', 'left')
            ->where('barang_detail.id_barang', $id_barang)
            ->get()
            ->getResultArray();
    }

    public function kurangiStok($id_barang, $jumlah = 1)
    {
        // Ambil stok saat ini
        $barang = $this->find($id_barang);
        if (!$barang || $barang['stok'] < $jumlah) {
            return false; // Jika stok tidak cukup, batalkan operasi
        }

        // Kurangi stok
        return $this->update($id_barang, ['stok' => $barang['stok'] - $jumlah]);
    }

    public function tambahStok($id_barang, $jumlah = 1)
    {
        // Ambil stok saat ini
        $barang = $this->find($id_barang);
        if (!$barang) {
            return false; // Jika barang tidak ditemukan, batalkan operasi
        }

        // Tambah stok
        return $this->update($id_barang, ['stok' => $barang['stok'] + $jumlah]);
    }

    
}
