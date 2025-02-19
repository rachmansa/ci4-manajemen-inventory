<?php
namespace App\Models;

use CodeIgniter\Model;

class BarangKeluarModel extends Model
{
    protected $table = 'barang_keluar';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_barang_detail', 'jumlah', 'tanggal_keluar','alasan','pihak_penerima', 'keterangan'];

    public function simpanBarangKeluar($data)
    {
        $this->db->transStart();
        
        // Simpan Barang Keluar
        $this->insert($data);
        
        // Cek apakah barang ada di Barang Lab
        $this->cekBarangDiBarangLab($data['id_barang_detail']);

        // Ubah status Barang Detail jika ada id_barang_detail
        if (!empty($data['id_barang_detail'])) {
            $this->updateStatusBarangDetail($data['id_barang_detail']);
        } else {
            // Kurangi stok barang jika tidak ada id_barang_detail
            $this->kurangiStokBarang($data['id_barang'], $data['jumlah']);
        }
        
        // Catat log pergerakan barang
        $this->catatLogPergerakan($data);
        
        $this->db->transComplete();
        return $this->db->transStatus();
    }

    public function hapusBarangKeluar($id)
    {
        $this->db->transStart();

        $barangKeluar = $this->find($id);
        if ($barangKeluar) {
            // Kembalikan stok atau ubah status barang detail
            if (!empty($barangKeluar['id_barang_detail'])) {
                $this->kembalikanStatusBarangDetail($barangKeluar['id_barang_detail']);
            } else {
                $this->tambahStokBarang($barangKeluar['id_barang'], $barangKeluar['jumlah']);
            }

            // Hapus data Barang Keluar
            $this->delete($id);

            // Catat log pergerakan barang
            $this->catatLogPembatalan($barangKeluar);
        }

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    private function cekBarangDiBarangLab($id_barang_detail)
    {
        $barangLabModel = new BarangLabModel();
        $barangLabModel->where('id_barang_detail', $id_barang_detail)->delete();
    }

    private function updateStatusBarangDetail($id_barang_detail)
    {
        $barangDetailModel = new BarangDetailModel();
        $barangDetailModel->update($id_barang_detail, ['status' => 'Penghapusan Aset']);
    }

    private function kembalikanStatusBarangDetail($id_barang_detail)
    {
        $barangDetailModel = new BarangDetailModel();
        $barangDetailModel->update($id_barang_detail, ['status' => 'Tersedia']);
    }

    private function kurangiStokBarang($id_barang, $jumlah)
    {
        $barangModel = new BarangModel();
        $barang = $barangModel->find($id_barang);
        if ($barang) {
            $barangModel->update($id_barang, ['stok' => $barang['stok'] - $jumlah]);
        }
    }

    private function tambahStokBarang($id_barang, $jumlah)
    {
        $barangModel = new BarangModel();
        $barang = $barangModel->find($id_barang);
        if ($barang) {
            $barangModel->update($id_barang, ['stok' => $barang['stok'] + $jumlah]);
        }
    }

    private function catatLogPergerakan($data)
    {
        $logModel = new LogPergerakanBarangModel();
        $logModel->insert([
            'id_barang_detail' => $data['id_barang_detail'] ?? null,
            'id_barang' => $data['id_barang'] ?? null,
            'tanggal' => date('Y-m-d H:i:s'),
            'keterangan' => 'Barang Keluar',
        ]);
    }

    private function catatLogPembatalan($data)
    {
        $logModel = new LogPergerakanBarangModel();
        $logModel->insert([
            'id_barang_detail' => $data['id_barang_detail'] ?? null,
            'id_barang' => $data['id_barang'] ?? null,
            'tanggal' => date('Y-m-d H:i:s'),
            'keterangan' => 'Pembatalan Barang Keluar',
        ]);
    }
}
