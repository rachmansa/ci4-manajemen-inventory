<?php

namespace App\Controllers;

use App\Models\JenisBarangModel;
use CodeIgniter\Controller;

class JenisBarangController extends Controller
{
    protected $jenisBarangModel;
    
    public function __construct()
    {
        $this->jenisBarangModel = new JenisBarangModel();
        helper(['form']);
    }

    public function index()
    {
        $data['jenisbarangs'] = $this->jenisBarangModel->findAll();
        return view('jenis-barang/index', $data);
    }

    public function create()
    {
        return view('jenis-barang/create');
    }

    public function store()
    {
        // Validasi input
        if (!$this->validate([
            'nama_jenis' => 'required|min_length[3]|max_length[255]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        try {
            $this->jenisBarangModel->save([
                'nama_jenis' => $this->request->getPost('nama_jenis')
            ]);
            return redirect()->to('/jenis-barang')->with('success', 'Jenis barang berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan jenis barang');
        }
    }

    public function edit($id)
    {
        $data['jenisbarang'] = $this->jenisBarangModel->find($id);

        if (!$data['jenisbarang']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jenis barang tidak ditemukan.');
        }

        return view('jenis-barang/edit', $data);
    }

    public function update($id)
    {
        // Validasi input
        if (!$this->validate([
            'nama_jenis' => 'required|min_length[3]|max_length[255]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Pastikan data ada sebelum update
        if (!$this->jenisBarangModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jenis barang tidak ditemukan.');
        }

        try {
            $this->jenisBarangModel->update($id, [
                'nama_jenis' => $this->request->getPost('nama_jenis')
            ]);
            return redirect()->to('/jenis-barang')->with('success', 'Jenis barang berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui jenis barang');
        }
    }

    public function delete($id)
    {
        // Pastikan data ada sebelum dihapus
        if (!$this->jenisBarangModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jenis barang tidak ditemukan.');
        }

        try {
            $this->jenisBarangModel->delete($id);
            return redirect()->to('/jenis-barang')->with('success', 'Jenis barang berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('/jenis-barang')->with('error', 'Gagal menghapus jenis barang');
        }
    }


}
