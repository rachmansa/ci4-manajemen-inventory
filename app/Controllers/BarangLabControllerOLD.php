<?php

namespace App\Controllers;

use App\Models\BarangLabModel;
use App\Models\BarangDetailModel;
use App\Models\LabModel;
use CodeIgniter\Controller;

class BarangLabController extends Controller
{
    protected $barangLabModel;
    protected $barangDetailModel;
    protected $labCatModel;
    protected $validation;
    protected $session;

    public function __construct()
    {
        $this->barangLabModel = new BarangLabModel();
        $this->barangDetailModel = new BarangDetailModel();
        $this->labCatModel = new LabModel();
        $this->validation = \Config\Services::validation();
        $this->session = session();
    }

    public function index()
    {
        $data['barang_labs'] = $this->barangLabModel->getAll();
        return view('barang-lab/index', $data);
    }

    public function create()
    {
        $data = [
            'barang_details' => $this->barangDetailModel->getAvailableForLab(), // Barang dengan id_jenis_penggunaan = 2
            'labs'           => $this->labCatModel->findAll(),
            'validation'     => $this->validation
        ];
        return view('barang-lab/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'id_barang_detail' => 'required|integer',
            'id_lab'           => 'required|integer',
            'nama_barang_lab'  => 'required|string|max_length[100]',
            'kondisi'          => 'required|in_list[Baik,Rusak,Diperbaiki]'
        ]);

        if (!$this->validate($validation->getRules())) {
            return redirect()->back()->withInput()->with('error', 'Harap isi semua bidang yang diperlukan.');
        }

        // Cek apakah barang_detail sudah ada di lab yang sama
        $existing = $this->barangLabModel
            ->where('id_barang_detail', $this->request->getPost('id_barang_detail'))
            ->where('id_lab', $this->request->getPost('id_lab'))
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Barang ini sudah terdaftar di Lab yang sama.');
        }

        $data = [
            'id_barang_detail'      => $this->request->getPost('id_barang_detail'),
            'serial_number'         => $this->request->getPost('serial_number') ?: null,
            'nomor_bmn'             => $this->request->getPost('nomor_bmn') ?: null,
            'id_lab'                => $this->request->getPost('id_lab'),
            'nama_barang_lab'       => $this->request->getPost('nama_barang_lab'),
            'kondisi'               => $this->request->getPost('kondisi'),
            'id_jenis_penggunaan'   => 2  // Lab CAT
        ];
        $id_barang_detail = $this->request->getPost('id_barang_detail');
        try {
            // Update status di barang_detail menjadi TERPAKAI
            $this->barangDetailModel->update($id_barang_detail, ['status' => 'TERPAKAI']);
            
            $this->barangLabModel->insert($data);
            
            return redirect()->to(base_url('barang-lab'))->with('success', 'Barang Lab berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }


    public function delete($id)
    {
        if ($this->barangLabModel->find($id)) {
            $this->barangLabModel->delete($id);
            $this->session->setFlashdata('success', 'Barang Lab berhasil dihapus.');
        } else {
            $this->session->setFlashdata('error', 'Data tidak ditemukan.');
        }
        return redirect()->to('/barang-lab');
    }
}
