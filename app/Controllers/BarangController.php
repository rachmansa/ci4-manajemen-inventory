<?php

namespace App\Controllers;

use App\Models\BarangModel;
use App\Models\SatuanBarangModel;
use App\Models\JenisBarangModel;
use CodeIgniter\Controller;

class BarangController extends Controller
{
    protected $barangModel;
    protected $satuanModel;
    protected $jenisModel;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
        $this->satuanModel = new SatuanBarangModel();
        $this->jenisModel = new JenisBarangModel();
    }

    public function index()
    {   
        $data['barangs'] = $this->barangModel->getBarangWithRelations();
        return view('barang/index', $data);
    }

    public function create()
    {
        $data = [
            'satuan' => $this->satuanModel->findAll(),
            'jenis' => $this->jenisModel->findAll()
        ];
        return view('barang/create', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'nama_barang'  => 'required|min_length[3]|max_length[100]',
            'stok'         => 'required|integer',
            'stok_minimal' => 'required|integer',
            'id_satuan'    => 'required|integer',
            'id_jenis'     => 'required|integer'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal. Harap isi semua kolom dengan benar.');
        }

        $this->barangModel->insert([
            'nama_barang'  => $this->request->getPost('nama_barang'),
            'stok'         => $this->request->getPost('stok'),
            'stok_minimal' => $this->request->getPost('stok_minimal'),
            'kode_barang'  => $this->request->getPost('kode_barang'),
            'deskripsi'    => $this->request->getPost('deskripsi'),
            'id_satuan'    => $this->request->getPost('id_satuan'),
            'id_jenis'     => $this->request->getPost('id_jenis')
        ]);

        return redirect()->to('/barang')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $barang = $this->barangModel->find($id);
        if (!$barang) {
            return redirect()->to('/barang')->with('error', 'Barang tidak ditemukan.');
        }

        $data = [
            'barang' => $barang,
            'satuan' => $this->satuanModel->findAll(),
            'jenis'  => $this->jenisModel->findAll()
        ];

        return view('barang/edit', $data);
    }

    public function update($id)
    {
        if (!$this->validate([
            'nama_barang'  => 'required|min_length[3]|max_length[100]',
            'stok'         => 'required|integer',
            'stok_minimal' => 'required|integer',
            'id_satuan'    => 'required|integer',
            'id_jenis'     => 'required|integer'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal. Harap isi semua kolom dengan benar.');
        }
        $this->barangModel->update($id, [
            'nama_barang'  => $this->request->getPost('nama_barang'),
            'stok'         => $this->request->getPost('stok'),
            'stok_minimal' => $this->request->getPost('stok_minimal'),
            'kode_barang'  => $this->request->getPost('kode_barang'),
            'deskripsi'    => $this->request->getPost('deskripsi'),
            'id_satuan'    => $this->request->getPost('id_satuan'),
            'id_jenis'     => $this->request->getPost('id_jenis')
        ]);

        return redirect()->to('/barang')->with('success', 'Barang berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (!$this->barangModel->find($id)) {
            return redirect()->to('/barang')->with('error', 'Barang tidak ditemukan.');
        }

        $this->barangModel->delete($id);
        return redirect()->to('/barang')->with('success', 'Barang berhasil dihapus.');
    }

    // Generate kode barang berdasarkan jenis
    public function generateKode($idJenis)
    {
        try {
            $kodeBarang = $this->generateKodeBarang($idJenis);
            return $this->response->setJSON(['success' => true, 'kode_barang' => $kodeBarang]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function generateKodeBarang($idJenis)
    {
        $jenis = $this->jenisModel->find($idJenis);
        if (!$jenis) {
            throw new \Exception('Jenis barang tidak ditemukan.');
        }

        $kodePrefix = strtoupper($jenis['nama_jenis']);
        $lastBarang = $this->barangModel->where('id_jenis', $idJenis)->orderBy('id_barang', 'DESC')->first();

        $lastNumber = 1;
        if ($lastBarang) {
            preg_match('/\d+$/', $lastBarang['kode_barang'], $matches);
            $lastNumber = isset($matches[0]) ? intval($matches[0]) + 1 : 1;
        }

        return $kodePrefix .'-'. sprintf('%04d', $lastNumber);
    }
}
