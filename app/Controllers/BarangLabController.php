<?php

namespace App\Controllers;

use App\Models\BarangLabModel;
use App\Models\BarangDetailModel;
use App\Models\BarangModel; // Tambahkan untuk stok barang
use App\Models\LabModel;
use CodeIgniter\Controller;

class BarangLabController extends Controller
{
    protected $barangLabModel;
    protected $barangDetailModel;
    protected $barangModel; // Tambahkan model Barang
    protected $labCatModel;
    protected $validation;
    protected $session;

    public function __construct()
    {
        $this->barangLabModel = new BarangLabModel();
        $this->barangDetailModel = new BarangDetailModel();
        $this->barangModel = new BarangModel(); // Inisialisasi model Barang
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
            'kondisi'          => 'required|in_list[Baik,Rusak,Diperbaiki]'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Harap isi semua bidang yang diperlukan.');
        }

        // Cek apakah barang_detail sudah ada di lab yang sama
        $existing = $this->barangLabModel
            ->where('id_barang_detail', $this->request->getPost('id_barang_detail'))
            ->where('id_lab', $this->request->getPost('id_lab'))
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Barang ini sudah terdaftar di Lab yang sama.');
        }

        $id_barang_detail = $this->request->getPost('id_barang_detail');
        $barang_detail = $this->barangDetailModel->find($id_barang_detail);

        if (!$barang_detail) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        $data = [
            'id_barang_detail'      => $id_barang_detail,
            'serial_number'         => $barang_detail['serial_number'] ?: null,
            'nomor_bmn'             => $barang_detail['nomor_bmn'] ?: null,
            'id_lab'                => $this->request->getPost('id_lab'),
            'nama_barang_lab'       => $this->request->getPost('nama_barang_lab'),
            'kondisi'               => $this->request->getPost('kondisi'),
            'id_jenis_penggunaan'   => 2  // Lab CAT
        ];

        try {
            // Update status barang_detail menjadi TERPAKAI
            $this->barangDetailModel->update($id_barang_detail, ['status' => 'TERPAKAI']);

            // Kurangi stok barang utama
            $this->barangModel->kurangiStok($barang_detail['id_barang']);

            // Simpan data ke barang_lab
            $this->barangLabModel->insert($data);

            return redirect()->to(base_url('barang-lab'))->with('success', 'Barang Lab berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        $barangLab = $this->barangLabModel->find($id);
        if (!$barangLab) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $data = [
            'barang_lab'       => $barangLab,
            'available_barang' => $this->barangDetailModel->getAvailableForLab($barangLab['id_barang_detail']),
            'labs'             => $this->labCatModel->findAll()
        ];

        return view('barang-lab/edit', $data);
    }

    public function update($id)
    {
        if (!$this->validate([
            'id_barang_detail' => 'required|integer',
            'id_lab'           => 'required|integer',
            'nama_barang_lab'  => 'required|string|max_length[100]',
            'kondisi'          => 'required|in_list[Baik,Rusak,Diperbaiki]'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Harap isi semua bidang yang diperlukan.');
        }

        $barangLab = $this->barangLabModel->find($id);
        if (!$barangLab) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $id_barang_detail_lama = $barangLab['id_barang_detail'];
        $id_barang_detail_baru = $this->request->getPost('id_barang_detail');

        try {
            $this->barangLabModel->update($id, [
                'id_barang_detail' => $id_barang_detail_baru,
                'id_lab'           => $this->request->getPost('id_lab'),
                'nama_barang_lab'  => $this->request->getPost('nama_barang_lab'),
                'kondisi'          => $this->request->getPost('kondisi')
            ]);

            // Jika barang diubah, update status dan stok
            if ($id_barang_detail_lama !== $id_barang_detail_baru) {
                $barang_detail_lama = $this->barangDetailModel->find($id_barang_detail_lama);
                $barang_detail_baru = $this->barangDetailModel->find($id_barang_detail_baru);

                // Barang lama jadi TERSEDIA & stok bertambah
                $this->barangDetailModel->update($id_barang_detail_lama, ['status' => 'TERSEDIA']);
                $this->barangModel->tambahStok($barang_detail_lama['id_barang']);

                // Barang baru jadi TERPAKAI & stok berkurang
                $this->barangDetailModel->update($id_barang_detail_baru, ['status' => 'TERPAKAI']);
                $this->barangModel->kurangiStok($barang_detail_baru['id_barang']);
            }

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
            $this->barangLabModel->delete($id);

            // Kembalikan status barang_detail jadi TERSEDIA
            $this->barangDetailModel->update($barangLab['id_barang_detail'], ['status' => 'TERSEDIA']);

            // Tambahkan stok barang utama
            $barang_detail = $this->barangDetailModel->find($barangLab['id_barang_detail']);
            $this->barangModel->tambahStok($barang_detail['id_barang']);

            return redirect()->to('/barang-lab')->with('success', 'Barang Lab berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
