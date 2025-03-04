<?php

namespace App\Controllers;

use App\Models\BarangDetailModel;
use App\Models\BarangModel;
use App\Models\JenisPenggunaanModel;
use App\Models\BarangLabModel;
use CodeIgniter\Controller;
use CodeIgniter\Database\BaseConnection;
use Picqer\Barcode\BarcodeGeneratorPNG;


class BarangDetailController extends Controller
{
    protected $barangDetailModel;
    protected $barangModel;
    protected $barangLabModel;
    protected $jenisPenggunaanModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->barangDetailModel = new BarangDetailModel();
        $this->barangModel = new BarangModel();
        $this->jenisPenggunaanModel = new JenisPenggunaanModel();
        $this->barangLabModel = new BarangLabModel();
        $this->session = session();
        $this->db = \Config\Database::connect(); // Inisialisasi database connection
    }

    private function getStatusClass($status)
    {
        switch ($status) {
            case 'tersedia': return 'success';
            case 'terpakai': return 'primary';
            case 'dipinjam': return 'warning';
            case 'menunggu diperbaiki': return 'info';
            case 'hilang': return 'dark';
            default: return 'secondary';
        }
    }

    private function getKondisiClass($kondisi)
    {
        switch ($kondisi) {
            case 'baik': return 'success';
            case 'rusak': return 'danger';
            case 'hilang': return 'dark';
            default: return 'secondary';
        }
    }

    public function index()
    {
        $barang_details = $this->barangDetailModel
            ->select('barang_detail.*, barang.nama_barang, jenis_penggunaan.nama_penggunaan')
            ->join('barang', 'barang.id_barang = barang_detail.id_barang')
            ->join('jenis_penggunaan', 'jenis_penggunaan.id_penggunaan = barang_detail.id_jenis_penggunaan')
            ->findAll();

        foreach ($barang_details as &$barang) {
            $barang['status_class'] = $this->getStatusClass($barang['status']);
            $barang['kondisi_class'] = $this->getKondisiClass($barang['kondisi']);

        }

        return view('barang-detail/index', ['barang_details' => $barang_details]);
    }

    public function create()
    {
        $data['barangs'] = $this->barangModel->getAvailableBarangForDetail();
        $data['jenis_penggunaan'] = $this->jenisPenggunaanModel->findAll();

        return view('barang-detail/create', $data);
    }


    public function store()
    {
        $idBarang = $this->request->getPost('id_barang');

        // Ambil data barang untuk mendapatkan stok
        $barang = $this->barangModel->find($idBarang);
        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        $stokBarang = $barang['stok'];

        // Hitung jumlah Barang Detail yang sudah ada
        $jumlahDetail = $this->barangDetailModel->where('id_barang', $idBarang)->countAllResults();

        if($stokBarang <= 0) {
            return redirect()->back()->with('error', 'Stok barang habis.');
        }

        // Validasi input
        if (!$this->validate([
            'id_barang' => 'required',
            'posisi_barang' => 'required',
            'id_jenis_penggunaan' => 'required',
            'tahun_barang' => 'required',
            'merk' => 'required',
            'serial_number' => 'permit_empty|is_unique[barang_detail.serial_number]',
            'nomor_bmn' => 'max_length[100]',
            'status' => 'required|in_list[tersedia,terpakai,dipinjam,menunggu diperbaiki, hilang, penghapusan aset]',
            'kondisi' => 'required|in_list[baik,rusak,hilang]',

        ])) {
            // print_r($this->validator->getErrors()); 
            // dd($this->request->getPost());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data. Pastikan semua input benar dan Nomor BMN serta Serial Number tidak duplikat.');
        }

        // Generate barcode unik
        $barcode = $this->barangDetailModel->generateBarcode();
        
        // Simpan Barang Detail
        $this->barangDetailModel->insert([
            'id_barang' => $idBarang,
            'posisi_barang' => $this->request->getPost('posisi_barang'),
            'id_jenis_penggunaan' => $this->request->getPost('id_jenis_penggunaan'),
            'serial_number' => $this->request->getPost('serial_number') ?: null,
            'merk' => $this->request->getPost('merk'),
            'nomor_bmn' => $this->request->getPost('nomor_bmn'),
            'tahun_barang' => $this->request->getPost('tahun_barang'),
            'status' => $this->request->getPost('status'),
            'kondisi' => $this->request->getPost('kondisi'),
            'barcode' => $barcode,
            
        ]);

        return redirect()->to('/barang-detail')->with('success', 'Barang detail berhasil ditambahkan.');
    }


    public function edit($id)
    {
        $barang_detail = $this->barangDetailModel->find($id);
        if (!$barang_detail) {
            return redirect()->to('/barang-detail')->with('error', 'Data tidak ditemukan.');
        }

        $data = [
            'barang_detail' => $barang_detail,
            'barangs' => $this->barangModel->findAll(),
            'jenisPenggunaan' => $this->jenisPenggunaanModel->findAll(),
        ];

        return view('barang-detail/edit', $data);
    }

    public function update($id)
    {
        $barangDetail = $this->barangDetailModel->find($id);

        if (!$barangDetail) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Validasi input
        if (!$this->validate([
            'id_barang' => 'required',
            'posisi_barang' => 'required',
            'id_jenis_penggunaan' => 'required',
            'tahun_barang' => 'required',
            'merk' => 'required',
            'status' => 'required',
            'kondisi' => 'required|in_list[baik,rusak,hilang]',

        ])) {
            // print_r($this->validator->getErrors()); 
            // dd($this->request->getPost());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data. Pastikan semua input benar dan Nomor BMN serta Serial Number tidak duplikat.');
        }

        $existingSerial = $this->barangDetailModel
            ->where('serial_number', $this->request->getPost('serial_number'))
            ->where('id_barang_detail !=', $id)
            ->first();

        

        if ($existingSerial) {
            return redirect()->back()->withInput()->with('error', 'Serial Number sudah digunakan.');
        }

     


        try {
            $this->barangDetailModel->update($id, [
                'id_barang' => $this->request->getPost('id_barang'),
                'posisi_barang' => $this->request->getPost('posisi_barang'),
                'id_jenis_penggunaan' => $this->request->getPost('id_jenis_penggunaan'),
                'serial_number' => $this->request->getPost('serial_number') ?: null,
                'nomor_bmn' => $this->request->getPost('nomor_bmn'),
                'merk' => $this->request->getPost('merk'),
                'tahun_barang' => $this->request->getPost('tahun_barang'),
                'status' => $this->request->getPost('status'),
                'kondisi' => $this->request->getPost('kondisi'),

            ]);

            return redirect()->to('/barang-detail')->with('success', 'Barang detail berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }


    // public function delete($id)
    // {
    //     $this->barangDetailModel->delete($id);
    //     return redirect()->to('/barang-detail')->with('success', 'Barang detail berhasil dihapus.');
    // }

    public function delete($id)
    {
        // Cek apakah barang detail ada
        $barangDetail = $this->barangDetailModel->find($id);
        if (!$barangDetail) {
            return redirect()->back()->with('error', 'Barang Detail tidak ditemukan.');
        }

        // Cek apakah barang digunakan dalam suatu kegiatan
        $jumlahBarangLab = $this->barangLabModel->where('id_barang_detail', $id)->countAllResults();
        // $jumlahBarangPegawai = $this->barangPegawaiUnitModel->where('id_barang_detail', $id)->countAllResults();
        // $jumlahBarangDipinjam = $this->barangDipinjamModel->where('id_barang_detail', $id)->countAllResults();
        
        // $totalDipakai = $jumlahBarangLab + $jumlahBarangPegawai + $jumlahBarangDipinjam;
        $totalDipakai = $jumlahBarangLab;

        if ($totalDipakai > 0) {
            try {
                // Mulai transaksi
                $this->db->transStart();

                // Tambahkan stok kembali ke tabel Barang
                $this->barangModel->where('id_barang', $barangDetail['id_barang'])
                                ->set('stok', 'stok + ' . $totalDipakai, false)
                                ->update();

                // Hapus data terkait di barang_lab, barang_pegawai_unit, dan barang_dipinjam
                $this->barangLabModel->where('id_barang_detail', $id)->delete();
                // $this->barangPegawaiUnitModel->where('id_barang_detail', $id)->delete();
                // $this->barangDipinjamModel->where('id_barang_detail', $id)->delete();

                // Hapus barang detail
                $this->barangDetailModel->delete($id);

                // Commit transaksi
                $this->db->transComplete();

                return redirect()->to('/barang-detail')->with('success', 'Barang Detail berhasil dihapus dan stok dikembalikan.');
            } catch (\Exception $e) {
                $this->db->transRollback();
                return redirect()->back()->with('error', 'Gagal menghapus Barang Detail. ' . $e->getMessage());
            }
        } else {
            // Jika tidak dipakai, hapus langsung
            $this->barangDetailModel->delete($id);
            return redirect()->to('/barang-detail')->with('success', 'Barang Detail berhasil dihapus.');
        }
    }

    public function updateByBarcode($barcode)
    {
        $barangDetail = $this->barangDetailModel->where('barcode', $barcode)->first();

        if (!$barangDetail) {
            return redirect()->to('/scan-barcode')->with('error', 'Barang tidak ditemukan.');
        }

        // Update kondisi barang menjadi "Terpakai"
        $this->barangDetailModel->update($barangDetail['id_barang_detail'], [
            'status' => 'terpakai',
        ]);

        return redirect()->to('/barang-detail')->with('success', 'Kondisi barang diperbarui.');
    }


}
