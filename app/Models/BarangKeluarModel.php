<?php
namespace App\Models;

use CodeIgniter\Model;

class BarangKeluarModel extends Model
{
    protected $table = 'barang_keluar';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_barang','id_barang_detail', 'jumlah', 'tanggal_keluar','alasan','pihak_penerima', 'keterangan'];

    public function getBarangKeluar()
    {
        return $this->select('
                barang_keluar.*, 
                barang.nama_barang, 
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        "id_barang_detail", barang_detail.id_barang_detail, 
                        "serial_number", barang_detail.serial_number, 
                        "nomor_bmn", barang_detail.nomor_bmn,
                        "merk", barang_detail.merk
                    )
                ) AS barang_details
            ')
            ->join('barang', 'barang.id_barang = barang_keluar.id_barang')
            ->join('barang_detail', 'FIND_IN_SET(barang_detail.id_barang_detail, barang_keluar.id_barang_detail) > 0', 'left')
            ->groupBy('barang_keluar.id_barang_keluar')
            ->orderBy('barang_keluar.tanggal_keluar', 'DESC')
            ->findAll();
    }

    
    public function simpanBarangKeluar($data)
    {
        $this->db->transStart();

        // Simpan Barang Keluar
        $this->insert($data);
        
        // Ubah status Barang Detail jika ada id_barang_detail
        if (!empty($data['id_barang_detail'])) {
            $id_barang_detail_list = explode(',', $data['id_barang_detail']); // Convert CSV ke array
            
            $this->updateStatusBarangDetail($id_barang_detail_list);

            // Hapus Barang dari Barang Lab dan Pegawai Unit
            // $this->cekBarangDiBarangLab($data['id_barang_detail']);
            $this->hapusDariLabDanPegawai($id_barang_detail_list);

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

        // Ambil semua Barang Keluar yang akan dihapus
        $barangKeluarList = $this->db->table('barang_keluar')
            ->whereIn('id_barang_keluar', (array) $id)
            ->get()
            ->getResultArray();

        if (!$barangKeluarList) {
            return false;
        }

        foreach ($barangKeluarList as $barangKeluar) {
            // Jika Barang memiliki detail (multiple select)
            if (!empty($barangKeluar['id_barang_detail'])) {
                $idBarangDetailList = explode(',', $barangKeluar['id_barang_detail']);

                foreach ($idBarangDetailList as $idBarangDetail) {
                    $this->kembalikanStatusBarangDetail(trim($idBarangDetail));

                    // Tambahkan stok kembali ke Barang
                    $this->tambahStokBarang($barangKeluar['id_barang'], 1); 

                    // Catat log pembatalan untuk setiap barang detail
                    $this->catatLogPembatalan([
                        'id_barang_keluar' => $barangKeluar['id_barang_keluar'],
                        'id_barang_detail' => trim($idBarangDetail),
                        'id_barang' => $barangKeluar['id_barang'],
                        'jumlah' => 1, // Karena tiap barang detail unik
                    ]);
                }
            } else {
                // Jika tidak ada Barang Detail, cukup tambah stok barang sesuai jumlahnya
                $this->tambahStokBarang($barangKeluar['id_barang'], $barangKeluar['jumlah']);
            }
        }

        // Hapus Barang Keluar
        $this->db->table('barang_keluar')->whereIn('id_barang_keluar', (array) $id)->delete();

        $this->db->transComplete();

        return $this->db->transStatus();
    }


    




    // private function cekBarangDiBarangLab($id_barang_detail)
    // {
    //     if (empty($id_barang_detail)) {
    //         return null; // Jika kosong, langsung return null
    //     }
        

    //     $barangLabModel = new BarangLabModel();

    //     if (is_array($id_barang_detail)) {
    //         $cek = $barangLabModel->whereIn('id_barang_detail', $id_barang_detail)->findAll();
    //         if ($cek) {
    //             $barangLabModel->whereIn('id_barang_detail', $id_barang_detail)->delete();
               
    //             // Debug setelah penghapusan
    //             // $cekLagi = $barangLabModel->whereIn('id_barang_detail', $id_barang_detail)->findAll();
    //             // dd("Setelah hapus", $cekLagi);
    //         }
    //     } else {
    //         $cek = $barangLabModel->where('id_barang_detail', $id_barang_detail)->first();
    //         if ($cek) {
    //             $barangLabModel->delete($cek['id_barang_lab']); // Hapus berdasarkan ID
    //         }
    //     }
    //     return $cek;
    // }



    private function updateStatusBarangDetail($id_barang_detail_list)
    {
        $barangDetailModel = new BarangDetailModel();

        if (!is_array($id_barang_detail_list)) {
            $id_barang_detail_list = [$id_barang_detail_list]; // Jika hanya satu, ubah ke array
        }

        $barangDetailModel->whereIn('id_barang_detail', $id_barang_detail_list)
                        ->set(['status' => 'Penghapusan Aset'])
                        ->update();
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

        if (!empty($data['id_barang_detail'])) {
            $id_barang_detail_list = explode(',', $data['id_barang_detail']);

            foreach ($id_barang_detail_list as $id_barang_detail) {
                $logModel->insert([
                    'id_barang_detail' => trim($id_barang_detail),
                    'id_barang' => $data['id_barang'],
                    'keterangan' => 'Barang Keluar',
                ]);
            }
        } else {
            $logModel->insert([
                'id_barang' => $data['id_barang'],
                'keterangan' => 'Barang Keluar',
            ]);
        }
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

    public function hapusDariLabDanPegawai($id_barang_detail)
    {
        // Hapus dari Barang Lab
        $this->db->table('barang_lab')->whereIn('id_barang_detail', $id_barang_detail)->delete();
        
        // Hapus dari Barang Pegawai
        // aktifkan jika Barang Pegawai sudah ada
        // $this->db->table('barang_pegawai_unit')->whereIn('id_barang_detail', $id_barang_detail)->delete();
    }
}
