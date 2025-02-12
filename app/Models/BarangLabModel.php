<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangLabModel extends Model
{
    protected $table            = 'barang_lab';
    protected $primaryKey       = 'id_barang_lab';
    protected $allowedFields    = ['id_barang', 'id_barang_detail', 'id_lab', 'id_jenis_penggunaan', 'jumlah'];

    /**
     * Get all Barang Lab data with related tables
     */
    public function getAll()
    {
        return $this->select('
                barang_lab.*, 
                barang.nama_barang, 
                barang_detail.serial_number, 
                lab_cat.nama_lab, 
                jenis_penggunaan.nama_penggunaan
            ')
            ->join('barang', 'barang.id_barang = barang_lab.id_barang')
            ->join('barang_detail', 'barang_detail.id_barang_detail = barang_lab.id_barang_detail', 'left')
            ->join('lab_cat', 'lab_cat.id_lab = barang_lab.id_lab')
            ->join('jenis_penggunaan', 'jenis_penggunaan.id_penggunaan = barang_lab.id_jenis_penggunaan')
            ->findAll();
    }
}
