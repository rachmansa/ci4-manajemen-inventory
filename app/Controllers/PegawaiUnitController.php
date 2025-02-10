<?php

namespace App\Controllers;

use App\Models\PegawaiUnitModel;
use CodeIgniter\Controller;

class PegawaiUnitController extends Controller
{
    protected $pegawaiUnitModel;
    
    public function __construct()
    {
        $this->pegawaiUnitModel = new PegawaiUnitModel();
        helper(['form']);
    }

    public function index()
    {
        $data['pegawai_units'] = $this->pegawaiUnitModel->findAll();
        return view('pegawai-unit/index', $data);
    }

    public function create()
    {
        return view('pegawai-unit/create');
    }

    public function store()
    {
        // Validasi input
        if (!$this->validate([
            'nip' => 'required|numeric|min_length[8]|max_length[18]',
            'nama_pegawai' => 'required|min_length[3]|max_length[255]',
            'unit_kerja' => 'required|min_length[3]|max_length[255]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        try {
            $this->pegawaiUnitModel->save([
                'nip' => $this->request->getPost('nip'),
                'nama_pegawai' => $this->request->getPost('nama_pegawai'),
                'unit_kerja' => $this->request->getPost('unit_kerja')
            ]);
            return redirect()->to('/pegawai-unit')->with('success', 'Pegawai unit berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan pegawai unit.');
        }
    }

    public function edit($id)
    {
        $data['pegawai_unit'] = $this->pegawaiUnitModel->find($id);

        if (!$data['pegawai_unit']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pegawai unit tidak ditemukan.');
        }

        return view('pegawai-unit/edit', $data);
    }

    public function update($id)
    {
        // Validasi input
        if (!$this->validate([
            'nip' => 'required|numeric|min_length[8]|max_length[18]',
            'nama_pegawai' => 'required|min_length[3]|max_length[255]',
            'unit_kerja' => 'required|min_length[3]|max_length[255]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Pastikan data ada sebelum update
        if (!$this->pegawaiUnitModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pegawai unit tidak ditemukan.');
        }

        try {
            $this->pegawaiUnitModel->update($id, [
                'nip' => $this->request->getPost('nip'),
                'nama_pegawai' => $this->request->getPost('nama_pegawai'),
                'unit_kerja' => $this->request->getPost('unit_kerja')
            ]);
            return redirect()->to('/pegawai-unit')->with('success', 'Pegawai unit berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui pegawai unit.');
        }
    }

    public function delete($id)
    {
        // Pastikan data ada sebelum dihapus
        if (!$this->pegawaiUnitModel->find($id)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Pegawai unit tidak ditemukan.');
        }

        try {
            $this->pegawaiUnitModel->delete($id);
            return redirect()->to('/pegawai-unit')->with('success', 'Pegawai unit berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->to('/pegawai-unit')->with('error', 'Gagal menghapus pegawai unit.');
        }
    }
}
