<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangDetailModel extends Model
{
    protected $table            = 'barang_detail';
    protected $primaryKey       = 'id_barang_detail';
    protected $allowedFields    = ['id_barang', 'serial_number', 'status', 'id_barang_dipinjam', 'created_at', 'updated_at'];
    protected $useTimestamps    = true;

    public function getAll()
    {
        return $this->select('barang_detail.*, barang.nama_barang')
            ->join('barang', 'barang.id_barang = barang_detail.id_barang')
            ->findAll();
    }

    public function getById($id)
    {
        return $this->where('id_barang_detail', $id)->first();
    }
}
