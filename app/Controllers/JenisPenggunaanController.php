<?php

namespace App\Controllers;

use App\Models\JenisPenggunaanModel;
use CodeIgniter\Controller;

class JenisPenggunaanController extends Controller
{
    protected $jenisPenggunaanModel;
    
    public function __construct()
    {
        $this->jenisPenggunaanModel = new JenisPenggunaanModel();
        helper(['form']);
    }

    public function index()
    {
        $data['jenispenggunaans'] = $this->jenisPenggunaanModel->findAll();
        return view('jenis-penggunaan/index', $data);
    }

    public function create()
    {
        return view('jenis-penggunaan/create');
    }

    public function store()
    {
        // Validasi input
        if (!$this->validate([
            'nama_penggunaan' => 'required|min_length[3]|max_length[255]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        try {
            $this->jenisPenggunaanModel->save([
                'nama_penggunaan' => $this->request->getPost('nama_penggunaan')
            ]);
            return redirect()->to('/jenis-penggunaan')->with('success', 'Jenis penggunaan berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan jenis penggunaan');
        }
    }

    public function edit($id)
    {
        $data['jenispenggunaan'] = $this->jenisPenggunaanModel->find($id);

        if (!$data['jenispenggunaan']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jenis penggunaan tidak ditemukan.');
        }

        return view('jenis-penggunaan/edit', $data);
    }

    public function update($id)
    {
        // Validasi input
        if (!$this->validate([
            'nama_penggunaan' => 'required|min_length[3]|max_length[255]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Pastikan data ada sebelum update
        if (!$this->jenisPenggunaanModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jenis penggunaan tidak ditemukan.');
        }

        try {
            $this->jenisPenggunaanModel->update($id, [
                'nama_penggunaan' => $this->request->getPost('nama_penggunaan')
            ]);
            return redirect()->to('/jenis-penggunaan')->with('success', 'Jenis penggunaan berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui jenis penggunaan');
        }
    }

    public function delete($id)
    {
        // Pastikan data ada sebelum dihapus
        if (!$this->jenisPenggunaanModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jenis penggunaan tidak ditemukan.');
        }

        try {
            $this->jenisPenggunaanModel->delete($id);
            return redirect()->to('/jenis-penggunaan')->with('success', 'Jenis penggunaan berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('/jenis-penggunaan')->with('error', 'Gagal menghapus jenis penggunaan');
        }
    }

}
