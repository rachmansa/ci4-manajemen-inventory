<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangKeluarModel extends Model
{
    protected $table            = 'barang_keluar';
    protected $primaryKey       = 'id_barang_keluar';
    protected $allowedFields    = [
        'id_barang', 'id_barang_detail', 'jumlah', 'tanggal_keluar', 'keterangan', 'alasan', 'pihak_penerima'
    ];

    // Relasi ke tabel Barang
    // public function getBarangKeluar()
    // {
    //     return $this->select('barang_keluar.*, barang.nama_barang, barang_detail.serial_number, barang_detail.nomor_bmn')
    //         ->join('barang', 'barang.id_barang = barang_keluar.id_barang')
    //         ->join('barang_detail', 'barang_detail.id_barang_detail = barang_keluar.id_barang_detail', 'left')
    //         ->orderBy('barang_keluar.tanggal_keluar', 'DESC')
    //         ->findAll();
    // }

    public function getBarangKeluar()
    {
        return $this->select('
                barang_keluar.*, 
                barang.nama_barang, 
                barang_keluar.id_barang_detail, 
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        "id_barang_detail", barang_detail.id_barang_detail, 
                        "serial_number", barang_detail.serial_number, 
                        "nomor_bmn", barang_detail.nomor_bmn,
                        "merk", barang_detail.merk
                    )
                ) AS barang_details
            ')
            ->join('barang', 'barang.id_barang = barang_keluar.id_barang')
            ->join('barang_detail', 'FIND_IN_SET(barang_detail.id_barang_detail, barang_keluar.id_barang_detail)', 'left')
            ->groupBy('barang_keluar.id_barang_keluar')
            ->orderBy('barang_keluar.tanggal_keluar', 'DESC')
            ->findAll();
    }

    
    // Mendapatkan data Barang Keluar berdasarkan ID
    public function getBarangKeluarById($id)
    {
        return $this->select('barang_keluar.*, barang.nama_barang, barang_detail.serial_number, barang_detail.nomor_bmn')
            ->join('barang', 'barang.id_barang = barang_keluar.id_barang')
            ->join('barang_detail', 'barang_detail.id_barang_detail = barang_keluar.id_barang_detail', 'left')
            ->where('barang_keluar.id_barang_keluar', $id)
            ->first();
    }

    public function updateStatusBarangDetail($id_barang_detail)
    {
        return $this->db->table('barang_detail')
            ->whereIn('id_barang_detail', $id_barang_detail)
            ->update([
                'status' => 'Penghapusan Aset'
            ]);
    }

    public function hapusDariLabDanPegawai($id_barang_detail)
    {
        // Hapus dari Barang Lab
        $this->db->table('barang_lab')->whereIn('id_barang_detail', $id_barang_detail)->delete();
        
        // Hapus dari Barang Pegawai
        $this->db->table('barang_pegawai_unit')->whereIn('id_barang_detail', $id_barang_detail)->delete();
    }


    public function getBarangDetailTersedia($id_barang)
    {
        return $this->db->table('barang_detail')
            ->where('id_barang', $id_barang)
            ->whereNotIn('id_barang_detail', function ($query) {
                $query->select('id_barang_detail')->from('barang_keluar');
            })
            ->get()->getResultArray();
    }


}
