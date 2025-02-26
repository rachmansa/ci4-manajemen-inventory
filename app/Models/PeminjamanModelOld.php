<?php

namespace App\Models;

use CodeIgniter\Model;

class PeminjamanModel extends Model
{
    protected $table            = 'peminjaman';
    protected $primaryKey       = 'id_peminjaman';
    protected $allowedFields    = [
        'pegawai_id', 'id_barang_detail', 'tanggal_pinjam',
        'tanggal_kembali', 'status', 'created_at', 'updated_at'
    ];
    protected $useTimestamps    = true;

    public function getAllPeminjaman()
    {
        return $this->db->table($this->table)
            ->select('peminjaman.*, pegawai_unit.nama_pegawai, pegawai_unit.unit_kerja, 
                    barang_detail.serial_number, barang_detail.nomor_bmn, barang.nama_barang')
            ->join('pegawai_unit', 'pegawai_unit.id_pegawai_unit = peminjaman.pegawai_id')
            ->join('barang_detail', 'barang_detail.id_barang_detail = peminjaman.id_barang_detail')
            ->join('barang', 'barang.id_barang = barang_detail.id_barang')
            ->get()
            ->getResultArray();
    }


    // Relasi dengan Pegawai
    public function getWithPegawai($id = null)
    {
        $builder = $this->db->table($this->table)
            ->select('peminjaman.*, pegawai_unit.nama_pegawai, pegawai_unit.unit_kerja')
            ->join('pegawai_unit', 'pegawai_unit.id_pegawai_unit = peminjaman.pegawai_id');

        return ($id) ? $builder->where('peminjaman.id_peminjaman', $id)->get()->getRowArray()
                     : $builder->get()->getResultArray();
    }

    // Relasi dengan Barang Detail
    public function getWithBarangDetail($id = null)
    {
        $builder = $this->db->table($this->table)
            ->select('peminjaman.*, barang_detail.serial_number, barang_detail.nomor_bmn, barang.nama_barang')
            ->join('barang_detail', 'barang_detail.id_barang_detail = peminjaman.id_barang_detail')
            ->join('barang', 'barang.id_barang = barang_detail.id_barang');

        return ($id) ? $builder->where('peminjaman.id_peminjaman', $id)->get()->getRowArray()
                     : $builder->get()->getResultArray();
    }

    // Cek apakah barang tersedia sebelum dipinjam
    public function isBarangAvailable($id_barang_detail)
    {
        $barangDetailModel = new \App\Models\BarangDetailModel();
        $barang = $barangDetailModel->find($id_barang_detail);

        return ($barang && $barang['status'] === 'Tersedia'); // Barang bisa dipinjam jika statusnya 'Tersedia'
    }

    // Perbarui status barang saat dipinjam
    public function pinjamBarang($id_barang_detail)
    {
        $barangDetailModel = new \App\Models\BarangDetailModel();
        return $barangDetailModel->update($id_barang_detail, ['status' => 'Terpakai']);
    }

    // Perbarui status barang saat dikembalikan
    public function kembalikanBarang($id_barang_detail)
    {
        $barangDetailModel = new \App\Models\BarangDetailModel();
        return $barangDetailModel->update($id_barang_detail, ['status' => 'Tersedia']);
    }
}
