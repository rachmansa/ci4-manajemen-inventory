<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangDetailModel extends Model
{
    protected $table            = 'barang_detail';
    protected $primaryKey       = 'id_barang_detail';
    protected $allowedFields    = ['id_barang', 'serial_number','id_posisi', 'id_jenis_penggunaan','nomor_bmn', 'tahun_barang','status', 'id_barang_dipinjam', 'created_at', 'updated_at'];
    protected $useTimestamps    = true;

    public function getBarangDetail($id = null)
    {
        $this->select('barang_detail.*, barang.nama_barang, posisi_barang.nama_posisi, jenis_penggunaan.nama_jenis');
        $this->join('barang', 'barang.id_barang = barang_detail.id_barang');
        $this->join('posisi_barang', 'posisi_barang.id = barang_detail.id_posisi');
        $this->join('jenis_penggunaan', 'jenis_penggunaan.id = barang_detail.id_jenis_penggunaan');

        if ($id !== null) {
            return $this->where('id_barang_detail', $id)->first();
        }

        return $this->findAll();
    }

    // Untuk Barang Lab

    public function getAvailableForLab()
    {
        $barangs = $this->db->table('barang_detail')
            ->select('barang_detail.*, barang.nama_barang')
            ->join('barang', 'barang.id_barang = barang_detail.id_barang')
            ->where('barang_detail.id_jenis_penggunaan', 2) // Hanya untuk Lab CAT
            ->whereNotIn('barang_detail.id_barang_detail', function($builder) {
                return $builder->select('id_barang_detail')->from('barang_lab');
            })
            ->get()
            ->getResultArray();

        // Format nama barang sesuai aturan
        $formattedBarang = [];
        foreach ($barangs as $barang) {
            $nama = $barang['nama_barang'];

            if (!empty($barang['serial_number'])) {
                $nama .= " - (" . $barang['serial_number'] . ")";
            } elseif (!empty($barang['nomor_bmn'])) {
                $nama .= " - (" . $barang['nomor_bmn'] . ")";
            }

            $formattedBarang[] = [
                'id_barang_detail' => $barang['id_barang_detail'],
                'nama_barang'      => $nama,
                'serial_number'    => $barang['serial_number'],
                'nomor_bmn'        => $barang['nomor_bmn']
            ];
        }

        return $formattedBarang;
    }

}
