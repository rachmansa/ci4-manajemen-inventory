<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangPegawaiUnitModel extends Model
{
    protected $table = 'barang_pegawai_unit';
    protected $primaryKey = 'id_barang_pegawai_unit';
    protected $allowedFields = [
        'id_barang', 'id_barang_detail', 'id_pegawai_unit', 'id_jenis_penggunaan',
        'jumlah', 'tanggal_serah_terima_awal', 'tanggal_serah_terima_akhir',
        'kondisi_barang', 'keterangan','created_at', 'updated_at', 'deleted_at'
    ];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    // Ambil semua data dengan join tabel barang & pegawai unit
    // public function getAll()
    // {
    //     return $this->select('barang_pegawai_unit.*, 
    //                           barang.nama_barang, 
    //                           pegawai_unit.nip, 
    //                           pegawai_unit.nama_pegawai, 
    //                           jenis_penggunaan.nama_penggunaan')
    //         ->join('barang', 'barang.id_barang = barang_pegawai_unit.id_barang')
    //         ->join('pegawai_unit', 'pegawai_unit.id_pegawai_unit = barang_pegawai_unit.id_pegawai_unit')
    //         ->join('jenis_penggunaan', 'jenis_penggunaan.id_penggunaan = barang_pegawai_unit.id_jenis_penggunaan')
    //         ->findAll();
    // }

    public function getAll()
    {
        return $this->select('
                pegawai_unit.id_pegawai_unit, 
                pegawai_unit.nip, 
                pegawai_unit.nama_pegawai, 
                COALESCE(GROUP_CONCAT(barang.nama_barang SEPARATOR ", "), "Belum memiliki barang") as daftar_barang
            ')
            ->from('pegawai_unit')
            ->join('barang_pegawai_unit bpu', 'pegawai_unit.id_pegawai_unit = bpu.id_pegawai_unit', 'left')
            ->join('barang', 'barang.id_barang = bpu.id_barang', 'left')
            ->groupBy('pegawai_unit.id_pegawai_unit, pegawai_unit.nip, pegawai_unit.nama_pegawai') // Pegawai tetap unik
            ->findAll();
    }



    // Ambil satu data berdasarkan ID dengan relasi
    public function findById($id)
    {
        return $this->select('barang_pegawai_unit.*, 
                              barang.nama_barang, 
                              pegawai_unit.nama_pegawai, 
                              jenis_penggunaan.nama_jenis_penggunaan')
            ->join('barang', 'barang.id_barang = barang_pegawai_unit.id_barang')
            ->join('pegawai_unit', 'pegawai_unit.id_pegawai_unit = barang_pegawai_unit.id_pegawai_unit')
            ->join('jenis_penggunaan', 'jenis_penggunaan.id_jenis_penggunaan = barang_pegawai_unit.id_jenis_penggunaan')
            ->where('barang_pegawai_unit.id_barang_pegawai_unit', $id)
            ->first();
    }
}
