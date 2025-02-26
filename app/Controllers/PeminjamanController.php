<?php

namespace App\Controllers;

use App\Models\PeminjamanModel;
use App\Models\PegawaiUnitModel;
use App\Models\BarangDetailModel;
use App\Models\BarangModel;



class PeminjamanController extends BaseController
{
    protected $peminjamanModel;
    protected $pegawaiUnitModel;
    protected $barangDetailModel;
    protected $db;

    public function __construct()
    {
        $this->peminjamanModel = new PeminjamanModel();
        $this->pegawaiUnitModel = new PegawaiUnitModel();
        $this->barangDetailModel = new BarangDetailModel();
        $this->barangModel = new BarangModel();
        $this->db = \Config\Database::connect();
    }

    // Menampilkan daftar peminjaman
    public function index()
    {
        $data = [
            'title' => 'Peminjaman Barang',
            'peminjaman' => $this->peminjamanModel->getAllPeminjaman()
        ];

        return view('peminjaman/index', $data);
    }

    // Menampilkan form tambah peminjaman
    public function create()
    {
        $data = [
            'title' => 'Form Peminjaman',
            'pegawai' => $this->pegawaiUnitModel->findAll(),
            'barang_detail' => $this->barangDetailModel->getAvailableItems(),
            'barang' => $this->barangModel->findAll()

        ];

        return view('peminjaman/create', $data);
    }

    public function store()
    {
        $validationRules = [
            'pegawai_id'       => 'required|integer|is_not_unique[pegawai_unit.id_pegawai_unit]',
            'id_barang_detail' => 'required'
        ];

        // Validasi input
        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('error', 'Harap pilih pegawai dan barang yang valid.');
        }

        $pegawai_id = $this->request->getPost('pegawai_id');
        $barang_detail_ids = $this->request->getPost('id_barang_detail');

        // Pastikan barang_detail_ids adalah array
        if (!is_array($barang_detail_ids) || empty($barang_detail_ids)) {
            return redirect()->back()->withInput()->with('error', 'Harap pilih minimal satu barang.');
        }

        $db = \Config\Database::connect();
        $db->transStart(); // Mulai transaksi

        foreach ($barang_detail_ids as $id_barang_detail) {
            // Ambil data barang detail
            $barangDetail = $this->barangDetailModel->find($id_barang_detail);
            
            if (!$barangDetail) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Barang tidak ditemukan.');
            }

            // Simpan data peminjaman
            $this->peminjamanModel->save([
                'pegawai_id'         => $pegawai_id,
                'id_barang'          => $barangDetail['id_barang'], // Ambil dari barang_detail
                'id_barang_detail'   => $id_barang_detail,
                'tanggal_peminjaman' => date('Y-m-d H:i:s'),
                'tanggal_pengembalian' => NULL, // Belum dikembalikan
                'kondisi_awal'       => $barangDetail['kondisi'], // Ambil kondisi awal barang
                'kondisi_akhir'      => NULL, // Belum dikembalikan
                'status'             => 'Dipinjam'
            ]);

            $pegawai = $this->pegawaiUnitModel->find($pegawai_id);
            $nama_pegawai = $pegawai ? $pegawai['nama_pegawai'] : 'Tidak Diketahui';
         
            // Update status barang menjadi "Terpakai"
            $this->barangDetailModel->update($id_barang_detail, [
                'status' => 'Terpakai',
                'posisi_barang' => 'Dipinjam oleh ' . $nama_pegawai
            ]);

            // Kurangi stok barang utama
            $this->barangModel->where('id_barang', $barangDetail['id_barang'])
                              ->set('stok', 'stok - 1', false)
                              ->update();
        }

        $db->transComplete(); // Selesaikan transaksi
        
        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }

        return redirect()->to('/peminjaman')->with('success', 'Peminjaman berhasil dilakukan.');
    }




    public function processReturn($id_peminjaman)
    {
        if (!$this->validate([
            'kondisi_akhir' => 'required'
        ])) {
            return redirect()->back()->with('error', 'Harap isi kondisi barang saat dikembalikan.');
        }

        try {
            $kondisi_akhir = $this->request->getPost('kondisi_akhir');
            $this->db->transBegin();
        
            if (!$this->peminjamanModel->kembalikanBarang($id_peminjaman, $kondisi_akhir)) {
                throw new \Exception("Barang gagal dikembalikan.");
            }

            $this->db->transCommit();
            return redirect()->to('/peminjaman')->with('success', 'Barang berhasil dikembalikan.');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->to('/peminjaman')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }



    // public function delete($id_peminjaman)
    // {
    //     try {
    //         $this->db->transBegin();

    //         $peminjaman = $this->peminjamanModel->find($id_peminjaman);

    //         if (!$peminjaman) {
    //             throw new \Exception("Data peminjaman tidak ditemukan.");
    //         }

    //         // Hapus peminjaman dan kembalikan status barang
    //         $this->peminjamanModel->delete($id_peminjaman);
            
    //         // Update status barang kembali menjadi "Tersedia" dan posisi menjadi "Penyimpanan Aset"
    //         $this->barangDetailModel->update($peminjaman['id_barang_detail'], [
    //             'status' => 'Tersedia',
    //             'posisi_barang' => 'Penyimpanan Aset'
    //         ]);

    //         // Tambahkan stok barang utama
    //         $this->barangModel->where('id_barang', $peminjaman['id_barang'])
    //                           ->set('stok', 'stok + 1', false)
    //                           ->update();


    //         $this->db->transCommit();
    //         return redirect()->to('/peminjaman')->with('success', 'Data peminjaman berhasil dihapus.');
    //     } catch (\Exception $e) {
    //         $this->db->transRollback();
    //         return redirect()->to('/peminjaman')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }

    public function delete($id_peminjaman)
    {
        $peminjaman = $this->peminjamanModel->find($id_peminjaman);

        if (!$peminjaman) {
            return redirect()->to('/peminjaman')->with('error', 'Data peminjaman tidak ditemukan.');
        }

        // Cek apakah status sudah "Dikembalikan"
        if ($peminjaman['status'] === 'Dikembalikan') {
            return redirect()->to('/peminjaman')->with('error', 'Peminjaman yang sudah dikembalikan tidak dapat dihapus.');
        }

        try {
            $this->db->transBegin();

            // Hapus peminjaman
            $this->peminjamanModel->delete($id_peminjaman);

            $this->db->transCommit();
            return redirect()->to('/peminjaman')->with('success', 'Peminjaman berhasil dihapus.');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->to('/peminjaman')->with('error', 'Terjadi kesalahan saat menghapus peminjaman.');
        }
    }


    public function getBarangDetail()
    {
        $id_barang = $this->request->getGet('id_barang');
        $barangDetailModel = new BarangDetailModel();

        $barangDetails = $barangDetailModel->where('id_barang', $id_barang)->where('status', 'tersedia')->findAll();


        $formattedDetails = array_map(function($detail) {
            return [
                'id_barang_detail' => $detail['id_barang_detail'],
                'nama_detail' => $detail['merk'] . ' - ' . 
                                (!empty($detail['serial_number']) ? $detail['serial_number'] : '(Tidak Ada)') . ' - ' . 
                                (!empty($detail['nomor_bmn']) ? $detail['nomor_bmn'] : '(Tidak Ada)')
            ];
        }, $barangDetails);

        return $this->response->setJSON($formattedDetails);
    }
}
