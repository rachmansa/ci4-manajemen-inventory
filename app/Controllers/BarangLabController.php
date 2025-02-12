<?php

namespace App\Controllers;

use App\Models\BarangLabModel;
use App\Models\BarangModel;
use App\Models\BarangDetailModel;
use App\Models\LabModel;
use App\Models\JenisPenggunaanModel;
use CodeIgniter\Controller;

class BarangLabController extends Controller
{
    protected $barangLabModel;
    protected $barangModel;
    protected $barangDetailModel;
    protected $labModel;
    protected $jenisPenggunaanModel;
    protected $session;

    public function __construct()
    {
        $this->barangLabModel = new BarangLabModel();
        $this->barangModel = new BarangModel();
        $this->barangDetailModel = new BarangDetailModel();
        $this->labModel = new LabModel();
        $this->jenisPenggunaanModel = new JenisPenggunaanModel();
        $this->session = session();
    }

    public function index()
    {
        $data = [
            'title' => 'Barang Lab',
            'barang_labs' => $this->barangLabModel->getAll()
        ];
        return view('barang-lab/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Barang Lab',
            'barangs' => $this->barangModel->findAll(),
            'labs' => $this->labModel->findAll(),
            'jenis_penggunaan' => $this->jenisPenggunaanModel->findAll()
        ];
        return view('barang-lab/create', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'id_barang' => 'required|integer',
            'id_lab' => 'required|integer',
            'jumlah' => 'required|integer'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Pastikan semua data diisi dengan benar.');
        }

        $id_barang = $this->request->getPost('id_barang');
        $jumlah = (int) $this->request->getPost('jumlah');

        $barang = $this->barangModel->find($id_barang);
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        $stok_tersedia = $barang['stok'];
        if ($jumlah > $stok_tersedia) {
            return redirect()->back()->withInput()->with('error', 'Jumlah barang melebihi stok tersedia!');
        }

        $id_barang_detail = $this->request->getPost('id_barang_detail') ?? [];

        // Gunakan transaksi database
        $this->barangLabModel->db->transStart();

        if (!empty($id_barang_detail)) {
            if (count($id_barang_detail) > $stok_tersedia) {
                return redirect()->back()->withInput()->with('error', 'Jumlah barang melebihi stok yang tersedia.');
            }

            foreach ($id_barang_detail as $id_detail) {
                $barangDetail = $this->barangDetailModel->find($id_detail);
                
                if (!$barangDetail) {
                    return redirect()->back()->withInput()->with('error', 'Barang dengan serial number tersebut tidak ditemukan.');
                }
                
                $serialNumber = $barangDetail['serial_number'];

                // **Cek apakah SN sudah ada di barang_lab**
                $existingSN = $this->barangLabModel->where('id_barang_detail', $id_detail)->first();
                if ($existingSN) {
                    return redirect()->back()->withInput()->with('error', "Serial Number $serialNumber sudah ada.");
                }

                // Validasi Jika stok id_barang_detail Masih Tersedia
                // $barangKeluar = $this->barangKeluarModel->where('id_barang_detail', $id_detail)->first();
                // $barangDipinjam = $this->barangDipinjamModel->where('id_barang_detail', $id_detail)->first();

                // if ($barangKeluar || $barangDipinjam) {
                //     return redirect()->back()->withInput()->with('error', "Serial Number $serialNumber tidak tersedia, sudah keluar atau sedang dipinjam.");
                // }

                $this->barangLabModel->insert([
                    'id_barang' => $id_barang,
                    'id_barang_detail' => $id_detail,
                    'id_lab' => $this->request->getPost('id_lab'),
                    'id_jenis_penggunaan' => 1,
                    'jumlah' => 1
                ]);
            }

            $jumlah = count($id_barang_detail);
        } else {
            $this->barangLabModel->insert([
                'id_barang' => $id_barang,
                'id_barang_detail' => null,
                'id_lab' => $this->request->getPost('id_lab'),
                'id_jenis_penggunaan' => 1,
                'jumlah' => $jumlah
            ]);
        }

        $this->barangModel->update($id_barang, ['stok' => $stok_tersedia - $jumlah]);

        $this->barangLabModel->db->transComplete();

        if ($this->barangLabModel->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }

        return redirect()->to(base_url('barang-lab'))->with('success', 'Barang Lab berhasil ditambahkan.');
    }




    public function edit($id)
    {
        $barangLab = $this->barangLabModel->find($id);

        if (!$barangLab) {
            return redirect()->to(base_url('barang-lab'))->with('error', 'Data tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Barang Lab',
            'barangLab' => $barangLab,
            'barangs' => $this->barangModel->findAll(),
            'labs' => $this->labModel->findAll(),
            'jenis_penggunaan' => $this->jenisPenggunaanModel->findAll()
        ];
        return view('barang-lab/edit', $data);
    }

    public function update($id)
    {
        if (!$this->validate([
            'id_barang' => 'required|integer',
            'id_lab' => 'required|integer',
            'jumlah' => 'required|integer'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Pastikan semua data diisi dengan benar.');
        }

        try {
            $data = [
                'id_barang' => $this->request->getPost('id_barang'),
                'id_barang_detail' => $this->request->getPost('id_barang_detail') ?? null,
                'id_lab' => $this->request->getPost('id_lab'),
                'id_jenis_penggunaan' => $this->request->getPost('id_jenis_penggunaan'),
                'jumlah' => $this->request->getPost('jumlah')
            ];

            $this->barangLabModel->update($id, $data);
            return redirect()->to(base_url('barang-lab'))->with('success', 'Barang Lab berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function delete($id)
    {
        try {
            $barangLab = $this->barangLabModel->find($id);
            if (!$barangLab) {
                return redirect()->back()->with('error', 'Barang Lab tidak ditemukan.');
            }

            $id_barang = $barangLab['id_barang'];
            $id_barang_detail = $barangLab['id_barang_detail'];
            $jumlah = $barangLab['jumlah'];

            if ($id_barang_detail) {
                // Jika barang memiliki serial number, kembalikan status barang_detail ke 'tersedia'
                $this->barangDetailModel->update($id_barang_detail, ['status' => 'tersedia']);
            } else {
                // Jika barang tidak memiliki serial number, kembalikan stok barang
                $barang = $this->barangModel->find($id_barang);
                if ($barang) {
                    $stok_terbaru = $barang['stok'] + $jumlah;
                    $this->barangModel->update($id_barang, ['stok' => $stok_terbaru]);
                }
            }

            // Hapus barang dari tabel barang_lab
            $this->barangLabModel->delete($id);

            return redirect()->to(base_url('barang-lab'))->with('success', 'Barang Lab berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }


    public function getSerials($id_barang)
    {
        $barangDetailModel = new BarangDetailModel();
        
        // Ambil daftar serial number barang berdasarkan id_barang
        $serials = $barangDetailModel->where('id_barang', $id_barang)
                                    ->where('status', 'tersedia') // Ambil yang belum dipakai
                                    ->findAll();

        // Jika tidak ada serial number, kembalikan array kosong
        return $this->response->setJSON($serials);
    }

    public function getStok()
    {
        $id_barang = $this->request->getPost('id_barang');

        $barang = $this->barangModel->find($id_barang);
        if (!$barang) {
            return $this->response->setJSON(['error' => 'Barang tidak ditemukan.']);
        }

        $stok_tersedia = $barang['stok'];

        return $this->response->setJSON(['stok' => $stok_tersedia]);
    }

    public function getBarangInfo()
    {
        $id_barang = $this->request->getPost('id_barang');

        $barang = $this->barangModel->find($id_barang);
        if (!$barang) {
            return $this->response->setJSON(['error' => 'Barang tidak ditemukan.']);
        }

        // Cek apakah barang punya serial number
        $barangDetail = $this->barangDetailModel->where('id_barang', $id_barang)->findAll();
        $serialNumbers = [];

        if (!empty($barangDetail)) {
            foreach ($barangDetail as $detail) {
                $serialNumbers[] = [
                    'id_barang_detail' => $detail['id_barang_detail'],
                    'serial_number' => $detail['serial_number']
                ];
            }
        }

        return $this->response->setJSON([
            'stok' => $barang['stok'],
            'serialNumbers' => $serialNumbers
        ]);
    }


}
