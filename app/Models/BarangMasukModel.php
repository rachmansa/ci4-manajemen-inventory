<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangMasukModel extends Model
{
    protected $table            = 'barang_masuk';
    protected $primaryKey       = 'id_barang_masuk';
    protected $allowedFields    = ['id_barang', 'id_jenis_penggunaan', 'jumlah', 'tanggal_masuk', 'keterangan'];

    // Relasi ke Barang
    public function getBarang()
    {
        return $this->db->table('barang')->get()->getResultArray();
    }

    // Relasi ke Jenis Penggunaan
    public function getJenisPenggunaan()
    {
        return $this->db->table('id_jenis_penggunaan')->get()->getResultArray();
    }
}
