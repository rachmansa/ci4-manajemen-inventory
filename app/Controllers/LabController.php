<?php

namespace App\Controllers;

use App\Models\LabModel;
use CodeIgniter\Controller;

class LabController extends Controller
{
    protected $labModel;
    
    public function __construct()
    {
        $this->labModel = new LabModel();
        helper(['form']);
    }

    public function index()
    {
        $data['labs'] = $this->labModel->findAll();
        return view('lab/index', $data);
    }

    public function create()
    {
        return view('lab/create');
    }

    public function store()
    {
        // Validasi input
        if (!$this->validate([
            'nama_lab' => 'required|min_length[3]|max_length[255]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        try {
            $this->labModel->save([
                'nama_lab' => $this->request->getPost('nama_lab')
            ]);
            return redirect()->to('/lab')->with('success', 'Lab berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan lab');
        }
    }

    public function edit($id)
    {
        $data['lab'] = $this->labModel->find($id);

        if (!$data['lab']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Lab tidak ditemukan.');
        }

        return view('lab/edit', $data);
    }

    public function update($id)
    {
        // Validasi input
        if (!$this->validate([
            'nama_lab' => 'required|min_length[3]|max_length[255]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Pastikan data ada sebelum update
        if (!$this->labModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Lab tidak ditemukan.');
        }

        try {
            $this->labModel->update($id, [
                'nama_lab' => $this->request->getPost('nama_lab')
            ]);
            return redirect()->to('/lab')->with('success', 'Lab berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui lab');
        }
    }

    public function delete($id)
    {
        // Pastikan data ada sebelum dihapus
        if (!$this->labModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Lab tidak ditemukan.');
        }

        try {
            $this->labModel->delete($id);
            return redirect()->to('/lab')->with('success', 'Lab berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('/lab')->with('error', 'Gagal menghapus lab');
        }
    }

}
