<?php

namespace App\Controllers;

use App\Models\SatuanBarangModel;
use CodeIgniter\Controller;

class SatuanBarangController extends Controller
{
    protected $satuanBarangModel;
    
    public function __construct()
    {
        $this->satuanBarangModel = new SatuanBarangModel(); 
        helper(['form']);
    }

    public function index()
    {
        $data['satuans'] = $this->satuanBarangModel->findAll();
        return view('satuan-barang/index', $data);
    }

    public function create()
    {
        return view('satuan-barang/create');
    }

    public function store()
    {
        // Validasi input
        if (!$this->validate([
            'nama_satuan' => 'required|min_length[2]|max_length[100]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        try {
            $this->satuanBarangModel->save([
                'nama_satuan' => $this->request->getPost('nama_satuan')
            ]);

            return redirect()->to('/satuan-barang')->with('success', 'Satuan barang berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan satuan barang.');
        }
    }

    public function edit($id)
    {
        $data['satuans'] = $this->satuanBarangModel->find($id);

        if (!$data['satuans']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Satuan barang tidak ditemukan.');
        }

        return view('satuan-barang/edit', $data);
    }

    public function update($id)
    {
        // Validasi input
        if (!$this->validate([
            'nama_satuan' => 'required|min_length[2]|max_length[100]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Pastikan data ada sebelum update
        if (!$this->satuanBarangModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Satuan barang tidak ditemukan.');
        }

        try {
            $this->satuanBarangModel->update($id, [
                'nama_satuan' => $this->request->getPost('nama_satuan')
            ]);

            return redirect()->to('/satuan-barang')->with('success', 'Satuan barang berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui satuan barang.');
        }
    }

    public function delete($id)
    {
        // Pastikan data ada sebelum dihapus
        if (!$this->satuanBarangModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Satuan barang tidak ditemukan.');
        }

        try {
            $this->satuanBarangModel->delete($id);
            return redirect()->to('/satuan-barang')->with('success', 'Satuan barang berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('/satuan-barang')->with('error', 'Gagal menghapus satuan barang.');
        }
    }

}
