<?php

namespace App\Controllers;

use App\Models\BarangDetailModel;
use App\Models\BarangModel;
use CodeIgniter\Controller;

class BarangDetailController extends Controller
{
    protected $barangDetailModel;
    protected $barangModel;
    protected $session;

    public function __construct()
    {
        $this->barangDetailModel = new BarangDetailModel();
        $this->barangModel = new BarangModel();
        $this->session = session();
    }

    private function getStatusClass($status)
    {
        switch ($status) {
            case 'tersedia': return 'success';
            case 'dipinjam': return 'warning';
            case 'rusak': return 'danger';
            case 'hilang': return 'dark';
            default: return 'secondary';
        }
    }


    public function index()
    {
        $barangDetailModel = new BarangDetailModel();
        $barang_details = $barangDetailModel->select('barang_detail.*, barang.nama_barang')
            ->join('barang', 'barang.id_barang = barang_detail.id_barang')
            ->findAll();

        // Tambahkan status class ke setiap barang_detail
        foreach ($barang_details as &$barang) {
            $barang['status_class'] = $this->getStatusClass($barang['status']);
        }

        return view('barang-detail/index', ['barang_details' => $barang_details]);
    }


    public function create()
    {
        $data['barangs'] = $this->barangModel->findAll();
        return view('barang-detail/create', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'id_barang' => 'required',
            'serial_number' => 'required|is_unique[barang_detail.serial_number]',
            'status' => 'required|in_list[tersedia,dipinjam,rusak,hilang]',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal.');
        }

        $this->barangDetailModel->insert([
            'id_barang' => $this->request->getPost('id_barang'),
            'serial_number' => $this->request->getPost('serial_number'),
            'status' => $this->request->getPost('status'),
        ]);

        return redirect()->to('/barang-detail')->with('success', 'Barang detail berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data['barang_detail'] = $this->barangDetailModel->getById($id);
        $data['barangs'] = $this->barangModel->findAll();

        if (!$data['barang_detail']) {
            return redirect()->to('/barang-detail')->with('error', 'Data tidak ditemukan.');
        }

        return view('barang-detail/edit', $data);
    }

    public function update($id)
    {
        if (!$this->validate([
            'id_barang' => 'required',
            'serial_number' => "required|is_unique[barang_detail.serial_number,id_barang_detail,$id]",
            'status' => 'required|in_list[tersedia,dipinjam,rusak,hilang]',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal.');
        }

        $this->barangDetailModel->update($id, [
            'id_barang' => $this->request->getPost('id_barang'),
            'serial_number' => $this->request->getPost('serial_number'),
            'status' => $this->request->getPost('status'),
        ]);

        return redirect()->to('/barang-detail')->with('success', 'Barang detail berhasil diperbarui.');
    }

    public function delete($id)
    {
        $this->barangDetailModel->delete($id);
        return redirect()->to('/barang-detail')->with('success', 'Barang detail berhasil dihapus.');
    }
}
