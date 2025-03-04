<?php

namespace App\Models;

use CodeIgniter\Model;

class LogPergerakanBarangModel extends Model
{
    protected $table            = 'log_pergerakan_barang';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id_barang', 'id_barang_detail', 'status', 
        'posisi_sebelumnya', 'posisi_sekarang', 
        'keterangan'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function tambahLog($id_barang, $id_barang_detail, $status, $posisi_sebelumnya, $posisi_sekarang, $keterangan = null)
    {
        return $this->save([
            'id_barang'         => $id_barang,
            'id_barang_detail'  => $id_barang_detail ?: null,
            'status'            => $status,
            'posisi_sebelumnya' => $posisi_sebelumnya,
            'posisi_sekarang'   => $posisi_sekarang,
            'keterangan'        => $keterangan
        ]);
    }


    /**
     * Ambil log pergerakan barang berdasarkan ID barang
     *
     * @param int $id_barang
     * @return array
     */
    public function getLogByBarang($id_barang)
    {
        return $this->where('id_barang', $id_barang)
                    ->orderBy('tanggal', 'DESC')
                    ->findAll();
    }

    // public function getLastPosition($id_barang_detail)
    // {
    //     return $this->where('id_barang_detail', $id_barang_detail)
    //                 ->orderBy('tanggal', 'DESC')
    //                 ->limit(1)
    //                 ->get()
    //                 ->getRow('posisi_sekarang');
    // }

    public function getLastPosition($id_barang_detail)
    {
        $query = $this->db->table('log_pergerakan_barang')
            ->where('id_barang_detail', $id_barang_detail)
            ->orderBy('tanggal', 'DESC')
            ->limit(1)
            ->get();

        // Cek apakah query berhasil atau tidak
        if (!$query || $query->getNumRows() == 0) {
            return null; // Jika tidak ada data, kembalikan null
        }

        return $query->getFirstRow('array'); // Ambil data pertama sebagai array
    }




}
