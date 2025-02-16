<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangMasukModel extends Model
{
    protected $table = 'barang_masuk';
    protected $primaryKey = 'id_barang_masuk';
    protected $allowedFields = ['id_barang', 'id_jenis_penggunaan', 'jumlah', 'tanggal_masuk'];

    public function getAll()
    {
        return $this->select('barang_masuk.*, barang.nama_barang, jenis_penggunaan.nama_penggunaan')
            ->join('barang', 'barang.id_barang = barang_masuk.id_barang')
            ->join('jenis_penggunaan', 'jenis_penggunaan.id_penggunaan = barang_masuk.id_jenis_penggunaan')
            ->orderBy('barang_masuk.tanggal_masuk', 'DESC')
            ->findAll();
    }

    public function insertBarangMasuk($id_barang, $id_jenis_penggunaan, $jumlah, $tanggal_masuk)
    {
        return $this->insert([
            'id_barang' => $id_barang,
            'id_jenis_penggunaan' => $id_jenis_penggunaan,
            'jumlah' => $jumlah,
            'tanggal_masuk' => $tanggal_masuk
        ]);
    }

    public function updateBarangMasuk($id, $id_barang, $id_jenis_penggunaan, $jumlah, $tanggal_masuk)
    {
        return $this->update($id, [
            'id_barang' => $id_barang,
            'id_jenis_penggunaan' => $id_jenis_penggunaan,
            'jumlah' => $jumlah,
            'tanggal_masuk' => $tanggal_masuk
        ]);
    }

    public function deleteBarangMasuk($id)
    {
        return $this->delete($id);
    }
}
