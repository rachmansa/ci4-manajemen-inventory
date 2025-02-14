<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangLabModel extends Model
{
    protected $table            = 'barang_lab';
    protected $primaryKey       = 'id_barang_lab';
    protected $allowedFields    = ['id_barang_detail', 'serial_number','nomor_bmn','id_lab', 'nama_barang_lab', 'kondisi', 'created_at', 'updated_at'];
    protected $useTimestamps    = true;

    public function getAll()
    {
        return $this->select('
                barang_lab.*, 
                barang.nama_barang, 
                barang_detail.serial_number, 
                barang_detail.nomor_bmn, 
                lab_cat.nama_lab
            ')
            ->join('barang_detail', 'barang_detail.id_barang_detail = barang_lab.id_barang_detail')
            ->join('barang', 'barang.id_barang = barang_detail.id_barang', 'left')
            ->join('lab_cat', 'lab_cat.id_lab = barang_lab.id_lab')
            ->findAll();
    }

    

}
