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
    protected $session;

    public function __construct()
    {
        $this->barangMasukModel = new BarangMasukModel();
        $this->barangModel = new BarangModel();
        $this->jenisPenggunaanModel = new JenisPenggunaanModel();
        $this->session = session();
    }

    public function index()
    {
        $data = [
            'barangmasuks' => $this->barangMasukModel->select('barang_masuk.*, barang.nama_barang as nama_barang, jenis_penggunaan.nama_penggunaan as nama_penggunaan')
                ->join('barang', 'barang.id_barang = barang_masuk.id_barang')
                ->join('jenis_penggunaan', 'jenis_penggunaan.id_penggunaan = barang_masuk.id_jenis_penggunaan')
                ->findAll()
        ];

        return view('barang-masuk/index', $data);
    }

    public function create()
    {
        $data = [
            'barang' => $this->barangModel->findAll(),
            'jenis_penggunaan' => $this->jenisPenggunaanModel->findAll()
        ];

        return view('barang-masuk/create', $data);
    }

    public function store()
    {
        if ($this->request->getMethod() === 'post') {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'id_barang' => 'required|numeric',
                'id_jenis_penggunaan' => 'required|numeric',
                'jumlah' => 'required|numeric|min_length[1]',
                'tanggal_masuk' => 'required|valid_date'
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return redirect()->back()->withInput()->with('error', 'Data tidak valid!');
            }

            $data = [
                'id_barang' => $this->request->getPost('id_barang'),
                'id_jenis_penggunaan' => $this->request->getPost('id_jenis_penggunaan'),
                'jumlah' => $this->request->getPost('jumlah'),
                'tanggal_masuk' => $this->request->getPost('tanggal_masuk')
            ];

            try {
                // Insert ke tabel barang_masuk
                $this->barangMasukModel->insert($data);

                // Ambil stok saat ini dari tabel barang
                $barang = $this->barangModel->find($data['id_barang']);
                if ($barang) {
                    $stok_baru = $barang['stok'] + $data['jumlah'];

                    // Update stok barang
                    $this->barangModel->update($data['id_barang'], ['stok' => $stok_baru]);
                }

                return redirect()->to('/barang-masuk')->with('success', 'Barang masuk berhasil ditambahkan dan stok diperbarui.');
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }
    }


    public function edit($id)
    {
        $barangMasuk = $this->barangMasukModel->find($id);

        if (!$barangMasuk) {
            return redirect()->to('/barang-masuk')->with('error', 'Data tidak ditemukan!');
        }

        $data = [
            'barang_masuk' => $barangMasuk,
            'barang' => $this->barangModel->findAll(),
            'jenis_penggunaan' => $this->jenisPenggunaanModel->findAll()
        ];

        return view('barang-masuk/edit', $data);
    }

    public function update($id)
    {
        if (!$this->validate([
            'id_barang' => 'required|integer',
            'id_jenis_penggunaan' => 'required|integer',
            'jumlah' => 'required|integer|min_length[1]',
            'tanggal_masuk' => 'required|valid_date',
            'keterangan' => 'permit_empty|max_length[255]'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid!');
        }

        try {
            $this->barangMasukModel->update($id, [
                'id_barang' => $this->request->getPost('id_barang'),
                'id_jenis_penggunaan' => $this->request->getPost('id_jenis_penggunaan'),
                'jumlah' => $this->request->getPost('jumlah'),
                'tanggal_masuk' => $this->request->getPost('tanggal_masuk'),
                'keterangan' => $this->request->getPost('keterangan')
            ]);

            return redirect()->to('/barang-masuk')->with('success', 'Barang masuk berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan!');
        }
    }

    public function delete($id)
    {
        try {
            $this->barangMasukModel->delete($id);
            return redirect()->to('/barang-masuk')->with('success', 'Barang masuk berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->to('/barang-masuk')->with('error', 'Terjadi kesalahan!');
        }
    }
}
