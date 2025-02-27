<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangDetailModel extends Model
{
    protected $table            = 'barang_detail';
    protected $primaryKey       = 'id_barang_detail';
    protected $allowedFields    = ['id_barang', 'serial_number',
    'posisi_barang', 'id_jenis_penggunaan','nomor_bmn', 'merk', 'tahun_barang',
    'status', 'kondisi','id_barang_dipinjam', 'barcode','created_at', 'updated_at'];
    protected $useTimestamps    = true;

    public function getBarangDetail($id = null)
    {
        $this->select('barang_detail.*, barang.nama_barang,  jenis_penggunaan.nama_jenis');
        $this->join('barang', 'barang.id_barang = barang_detail.id_barang');
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
            ->where('barang_detail.status !=', 'penghapusan aset') // Exclude items with status 'penghapusan aset'
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
                'nomor_bmn'        => $barang['nomor_bmn'],
             
            ];
        }

        return $formattedBarang;
    }

    public function getAvailableItems()
    {
        $barangs = $this->db->table('barang_detail')
            ->select('barang_detail.*, barang.nama_barang')
            ->join('barang', 'barang.id_barang = barang_detail.id_barang')
            ->whereNotIn('barang_detail.id_barang_detail', function($builder) {
                return $builder->select('id_barang_detail')->from('barang_lab');
            })
            ->where('barang_detail.status !=', 'penghapusan aset') // Exclude items with status 'penghapusan aset'
            ->where('barang_detail.status =', 'tersedia') // include items with status 'tersedia'
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
                'nomor_bmn'        => $barang['nomor_bmn'],
             
            ];
        }

        return $formattedBarang;
    }

    

    public function getDetailWithBarang($id_barang_detail)
    {
        return $this->select('barang_detail.*, barang.nama_barang, barang.stok')
            ->join('barang', 'barang.id_barang = barang_detail.id_barang')
            ->where('barang_detail.id_barang_detail', $id_barang_detail)
            ->first();
    }

    public function getCurrentPositionByBarang($id_barang)
    {
        return $this->where('id_barang', $id_barang)
                    ->limit(1) // Ambil salah satu data
                    ->get()
                    ->getRow('posisi');
    }

    public function getCurrentPosition($id_barang_detail)
    {
        $query = $this->db->table('barang_detail')
            ->select('posisi_barang')
            ->where('id_barang_detail', $id_barang_detail) // Gunakan field yang benar
            ->get();

        if (!$query || $query->getNumRows() == 0) {
            return null; // Jika tidak ada data, kembalikan null
        }

        return $query->getFirstRow('array')['posisi_barang'] ?? null;
    }

    public function generateBarcode()
    {
        return 'BD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }


}
