<?php

namespace App\Controllers;

use App\Models\PosisiBarangModel;
use CodeIgniter\Controller;

class PosisiBarangController extends Controller
{
    protected $posisiBarangModel;
    
    public function __construct()
    {
        $this->posisiBarangModel = new PosisiBarangModel();
        helper(['form']);
    }

    public function index()
    {
        $data['posisis'] = $this->posisiBarangModel->findAll();
        return view('posisi-barang/index', $data);
    }

    public function create()
    {
        return view('posisi-barang/create');
    }

    public function store()
    {
        // Validasi input
        if (!$this->validate([
            'nama_posisi' => 'required|min_length[3]|max_length[255]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        try {
            $this->posisiBarangModel->save([
                'nama_posisi' => $this->request->getPost('nama_posisi')
            ]);
            return redirect()->to('/posisi-barang')->with('success', 'Posisi berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan posisi');
        }
    }

    public function edit($id)
    {
        $data['posisi'] = $this->posisiBarangModel->find($id);

        if (!$data['posisi']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Posisi tidak ditemukan.');
        }

        return view('posisi-barang/edit', $data);
    }

    public function update($id)
    {
        // Validasi input
        if (!$this->validate([
            'nama_posisi' => 'required|min_length[3]|max_length[255]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Pastikan data ada sebelum update
        if (!$this->posisiBarangModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Posisi tidak ditemukan.');
        }

        try {
            $this->posisiBarangModel->update($id, [
                'nama_posisi' => $this->request->getPost('nama_posisi')
            ]);
            return redirect()->to('/posisi-barang')->with('success', 'Posisi berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui posisi');
        }
    }

    public function delete($id)
    {
        // Pastikan data ada sebelum dihapus
        if (!$this->posisiBarangModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Posisi tidak ditemukan.');
        }

        try {
            $this->posisiBarangModel->delete($id);
            return redirect()->to('/posisi-barang')->with('success', 'Posisi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('/posisi-barang')->with('error', 'Gagal menghapus posisi');
        }
    }

}
