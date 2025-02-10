<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table            = 'barang';
    protected $primaryKey       = 'id_barang';
    protected $allowedFields    = [
        'nama_barang', 'stok', 'stok_minimal', 'kode_barang',
        'deskripsi', 'id_satuan', 'id_jenis'
    ];

    // Helper untuk mengambil data barang dengan join ke satuan_barang & jenis_barang
    public function getBarangWithRelations()
    {
        return $this->select('barang.*, satuan_barang.nama_satuan, jenis_barang.nama_jenis')
                    ->join('satuan_barang', 'satuan_barang.id_satuan = barang.id_satuan')
                    ->join('jenis_barang', 'jenis_barang.id_jenis = barang.id_jenis')
                    ->findAll();
    }
    
}
