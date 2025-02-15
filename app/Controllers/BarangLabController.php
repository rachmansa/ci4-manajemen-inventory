<?php

namespace App\Controllers;

use App\Models\BarangLabModel;
use App\Models\BarangDetailModel;
use App\Models\BarangModel;
use App\Models\LabModel;
use CodeIgniter\Controller;

class BarangLabController extends Controller
{
    protected $barangLabModel;
    protected $barangDetailModel;
    protected $barangModel;
    protected $labCatModel;
    protected $validation;
    protected $session;

    public function __construct()
    {
        $this->barangLabModel = new BarangLabModel();
        $this->barangDetailModel = new BarangDetailModel();
        $this->barangModel = new BarangModel(); 
        $this->labCatModel = new LabModel();
        $this->validation = \Config\Services::validation();
        $this->session = session();
    }

    public function index()
    {
        $data['barang_labs'] = $this->barangLabModel->getAll();
        return view('barang-lab/index', $data);
    }

    public function create()
    {
        $data = [
            'barang_details' => $this->barangDetailModel->getAvailableForLab(), // Barang dengan id_jenis_penggunaan = 2
            'labs'           => $this->labCatModel->findAll(),
            'validation'     => $this->validation
        ];
        return view('barang-lab/create', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'id_barang_detail' => 'required|integer',
            'id_lab'           => 'required|integer',
            'nama_barang_lab'  => 'required|string|max_length[100]',
            'kondisi'          => 'required|in_list[Baik,Rusak,Diperbaiki]',
            'jumlah'           => 'if_exist|integer|greater_than[0]'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Harap isi semua bidang yang diperlukan.');
        }

        $id_barang_detail = $this->request->getPost('id_barang_detail');
        $barang_detail = $this->barangDetailModel->find($id_barang_detail);
        $barang = $this->barangModel->find($barang_detail['id_barang']);
        $jumlah = (int) $this->request->getPost('jumlah') ?: 1;

        // Cek apakah barang_detail sudah ada di lab yang sama
        $existing = $this->barangLabModel
            ->where('id_barang_detail', $this->request->getPost('id_barang_detail'))
            ->where('id_lab', $this->request->getPost('id_lab'))
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Barang ini sudah terdaftar di Lab yang sama.');
        }

        if (!$barang_detail) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        // **Validasi stok**
        if ($jumlah > $barang['stok']) {
            return redirect()->back()->withInput()->with('error', 'Jumlah melebihi stok yang tersedia.');
        }

        $data = [
            'id_barang_detail'      => $id_barang_detail,
            'serial_number'         => $barang_detail['serial_number'] ?: null,
            'nomor_bmn'             => $barang_detail['nomor_bmn'] ?: null,
            'id_lab'                => $this->request->getPost('id_lab'),
            'nama_barang_lab'       => $this->request->getPost('nama_barang_lab'),
            'kondisi'               => $this->request->getPost('kondisi'),
            'id_jenis_penggunaan'   => 2,  // Lab CAT
            'jumlah'                => $jumlah
        ];

        try {
            // Update status barang_detail menjadi TERPAKAI
            $this->barangDetailModel->update($id_barang_detail, ['status' => 'TERPAKAI']);

            // Kurangi stok barang utama
            $this->barangModel->kurangiStok($barang_detail['id_barang'], $jumlah);

            // Simpan data ke barang_lab
            $this->barangLabModel->insert($data);

            return redirect()->to(base_url('barang-lab'))->with('success', 'Barang Lab berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    // public function edit($id)
    // {
    //     $barangLab = $this->barangLabModel->find($id);
    //     if (!$barangLab) {
    //         return redirect()->back()->with('error', 'Data tidak ditemukan.');
    //     }

    //     // Ambil data barang detail (untuk mendapatkan id_barang)
    //     $barangDetail = $this->barangDetailModel->find($barangLab['id_barang_detail']);
    //     if (!$barangDetail) {
    //         return redirect()->to(base_url('barang-lab'))->with('error', 'Barang detail tidak ditemukan.');
    //     }

    //     // Ambil stok barang utama
    //     $barang = $this->barangModel->find($barangDetail['id_barang']);
    //     $stokTersedia = $barang ? $barang['stok'] : 0;
        
    //     $data = [
    //         'barang_lab'       => $barangLab,
    //         'labs'             => $this->labCatModel->findAll(),
    //         'stok_tersedia' => $stokTersedia,
    //     ];

    //     return view('barang-lab/edit', $data);
    // }

    public function edit($id)
    {
        $barangLab = $this->barangLabModel->find($id);
        if (!$barangLab) {
            return redirect()->to(base_url('barang-lab'))->with('error', 'Data tidak ditemukan.');
        }

        // Gunakan method di model untuk mengambil data barang detail
        $barangDetail = $this->barangDetailModel->getDetailWithBarang($barangLab['id_barang_detail']);
        if (!$barangDetail) {
            return redirect()->to(base_url('barang-lab'))->with('error', 'Barang detail tidak ditemukan.');
        }

        $data = [
            'title'         => 'Edit Barang Lab',
            'barang_lab'    => $barangLab,
            'barang_detail' => $barangDetail,
            'labs'          => $this->labCatModel->findAll(),
            'stok_tersedia' => $barangDetail['stok'], 
        ];

        return view('barang-lab/edit', $data);
    }


    public function update($id)
    {
        if (!$this->validate([
            'id_lab'           => 'required|integer',
            'nama_barang_lab'  => 'required|string|max_length[100]',
            'kondisi'          => 'required|in_list[Baik,Rusak,Diperbaiki]',
            'jumlah'           => 'if_exist|integer|greater_than[0]'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Harap isi semua bidang yang diperlukan.');
        }

        $barangLab = $this->barangLabModel->find($id);
        if (!$barangLab) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $id_barang = $barangLab['id_barang'];
        $barang = $this->barangModel->find($id_barang);
        $jumlah_lama = $barangLab['jumlah'] ?? 1;
        $jumlah_baru = $this->request->getPost('jumlah') ?: 1;

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        // **Validasi stok jika jumlah bertambah**
        if ($jumlah_baru > $jumlah_lama) {
            $stok_tersedia = $barang['stok'];
            $selisih = $jumlah_baru - $jumlah_lama;

            if ($selisih > $stok_tersedia) {
                return redirect()->back()->withInput()->with('error', 'Jumlah melebihi stok yang tersedia.');
            }
        }

        try {
            // **Update stok barang jika jumlah berubah**
            if ($jumlah_baru > $jumlah_lama) {
                $this->barangModel->kurangiStok($id_barang, $jumlah_baru - $jumlah_lama);
            } elseif ($jumlah_baru < $jumlah_lama) {
                $this->barangModel->tambahStok($id_barang, $jumlah_lama - $jumlah_baru);
            }

            // **Update data barang lab**
            $this->barangLabModel->update($id, [
                'id_lab'           => $this->request->getPost('id_lab'),
                'nama_barang_lab'  => $this->request->getPost('nama_barang_lab'),
                'kondisi'          => $this->request->getPost('kondisi'),
                'jumlah'           => $jumlah_baru
            ]);

            return redirect()->to('/barang-lab')->with('success', 'Barang Lab berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan, coba lagi.');
        }
    }

    public function delete($id)
    {
        $barangLab = $this->barangLabModel->find($id);
        if (!$barangLab) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        try {
            // Ambil data barang_detail terkait
            $barang_detail = $this->barangDetailModel->find($barangLab['id_barang_detail']);
            
            if (!$barang_detail) {
                return redirect()->back()->with('error', 'Barang detail tidak ditemukan.');
            }

            // **Hapus data dari tabel barang_lab**
            $this->barangLabModel->delete($id);

            // **Jika barang punya SN/BMN**
            if ($barang_detail['serial_number'] || $barang_detail['nomor_bmn']) {
                $updateStatus = $this->barangDetailModel->update($barangLab['id_barang_detail'], ['status' => 'TERSEDIA']);
                if ($updateStatus) {
                    $this->barangModel->tambahStok($barang_detail['id_barang'], 1);
                }
            } else {
                // **Jika barang tanpa SN/BMN, ubah status dan kembalikan stok**
                $updateStatus = $this->barangDetailModel->update($barangLab['id_barang_detail'], ['status' => 'TERSEDIA']);
                if ($updateStatus) {
                    $this->barangModel->tambahStok($barang_detail['id_barang'], $barangLab['jumlah']);
                }
            }

            return redirect()->to('/barang-lab')->with('success', 'Barang Lab berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }






}
