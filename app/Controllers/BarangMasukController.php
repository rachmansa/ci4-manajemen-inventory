<?php

namespace App\Controllers;

use App\Models\BarangMasukModel;
use App\Models\BarangModel;
use App\Models\JenisPenggunaanModel;
use CodeIgniter\Controller;

class BarangMasukController extends Controller
{
    protected $barangMasukModel;
    protected $barangModel;
    protected $jenisPenggunaanModel;
    protected $validation;
    protected $session;

    public function __construct()
    {
        $this->barangMasukModel = new BarangMasukModel();
        $this->barangModel = new BarangModel();
        $this->jenisPenggunaanModel = new JenisPenggunaanModel();
        $this->validation = \Config\Services::validation();
        $this->session = session();
    }

    public function index()
    {
        $data['barang_masuk'] = $this->barangMasukModel->getAll();
        return view('barang-masuk/index', $data);
    }

    public function create()
    {
        $data = [
            'barang' => $this->barangModel->findAll(),
            'jenis_penggunaan' => $this->jenisPenggunaanModel->findAll(),
            'validation' => $this->validation
        ];
        return view('barang-masuk/create', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'id_barang' => 'required|integer',
            'id_jenis_penggunaan' => 'required|integer',
            'jumlah' => 'required|integer|greater_than[0]',
            'tanggal_masuk' => 'required|valid_date'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Harap isi semua bidang yang diperlukan.');
        }

        $id_barang = $this->request->getPost('id_barang');
        $jumlah = (int) $this->request->getPost('jumlah');
        $id_jenis_penggunaan = $this->request->getPost('id_jenis_penggunaan');
        $tanggal_masuk = $this->request->getPost('tanggal_masuk');

        $data = $this->request->getPost();

        // Ambil tanggal hari ini
        $tanggal_sekarang = date('Y-m-d');

        // Validasi tanggal masuk tidak boleh lebih dari hari ini
        if ($data['tanggal_masuk'] > $tanggal_sekarang) {
            return redirect()->back()->withInput()->with('error', 'Tanggal masuk tidak boleh lebih dari hari ini.');
        }

        try {
            // Simpan ke database
            $this->barangMasukModel->insertBarangMasuk($id_barang, $id_jenis_penggunaan, $jumlah, $tanggal_masuk);

            // Tambah stok barang
            $this->barangModel->tambahStok($id_barang, $jumlah);

            return redirect()->to('/barang-masuk')->with('success', 'Barang masuk berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($id)
    {
        $barangMasuk = $this->barangMasukModel->find($id);
        if (!$barangMasuk) {
            return redirect()->to('/barang-masuk')->with('error', 'Data tidak ditemukan.');
        }

        $data = [
            'barang_masuk' => $barangMasuk,
            'barang' => $this->barangModel->findAll(),
            'jenis_penggunaan' => $this->jenisPenggunaanModel->findAll(),
            'validation' => $this->validation
        ];
        return view('barang-masuk/edit', $data);
    }

    public function update($id)
    {
        if (!$this->validate([
            'id_barang' => 'required|integer',
            'id_jenis_penggunaan' => 'required|integer',
            'jumlah' => 'required|integer|greater_than[0]',
            'tanggal_masuk' => 'required|valid_date'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Harap isi semua bidang yang diperlukan.');
        }

        $barangMasuk = $this->barangMasukModel->find($id);
        if (!$barangMasuk) {
            return redirect()->to('/barang-masuk')->with('error', 'Data tidak ditemukan.');
        }

        $id_barang = $this->request->getPost('id_barang');
        $jumlah_baru = (int) $this->request->getPost('jumlah');
        $id_jenis_penggunaan = $this->request->getPost('id_jenis_penggunaan');
        $tanggal_masuk = $this->request->getPost('tanggal_masuk');

        $jumlah_lama = $barangMasuk['jumlah'];
        $selisih = $jumlah_baru - $jumlah_lama;

        $data = $this->request->getPost();

        // Ambil tanggal hari ini
        $tanggal_sekarang = date('Y-m-d');

        // Validasi tanggal masuk tidak boleh lebih dari hari ini
        if ($data['tanggal_masuk'] > $tanggal_sekarang) {
            return redirect()->back()->withInput()->with('error', 'Tanggal masuk tidak boleh lebih dari hari ini.');
        }

        try {
            // Update data barang masuk
            $this->barangMasukModel->updateBarangMasuk($id, $id_barang, $id_jenis_penggunaan, $jumlah_baru, $tanggal_masuk);

            // Perbarui stok barang berdasarkan perubahan jumlah
            if ($selisih > 0) {
                $this->barangModel->tambahStok($id_barang, $selisih);
            } elseif ($selisih < 0) {
                $this->barangModel->kurangiStok($id_barang, abs($selisih));
            }

            return redirect()->to('/barang-masuk')->with('success', 'Barang masuk berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function delete($id)
    {
        $barangMasuk = $this->barangMasukModel->find($id);
        if (!$barangMasuk) {
            return redirect()->to('/barang-masuk')->with('error', 'Data tidak ditemukan.');
        }

        $id_barang = $barangMasuk['id_barang'];
        $jumlah = $barangMasuk['jumlah'];

        try {
            // Hapus data barang masuk
            $this->barangMasukModel->deleteBarangMasuk($id);

            // Kurangi stok barang
            $this->barangModel->kurangiStok($id_barang, $jumlah);

            return redirect()->to('/barang-masuk')->with('success', 'Barang masuk berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
