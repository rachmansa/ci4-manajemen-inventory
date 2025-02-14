<?php

namespace App\Controllers;

use App\Models\BarangDetailModel;
use App\Models\BarangModel;
use App\Models\PosisiBarangModel;
use App\Models\JenisPenggunaanModel;
use App\Models\BarangLabModel;
use CodeIgniter\Controller;
use CodeIgniter\Database\BaseConnection;

class BarangDetailController extends Controller
{
    protected $barangDetailModel;
    protected $barangModel;
    protected $barangLabModel;
    protected $posisiBarangModel;
    protected $jenisPenggunaanModel;
    protected $session;
    protected $db;

    public function __construct()
    {
        $this->barangDetailModel = new BarangDetailModel();
        $this->barangModel = new BarangModel();
        $this->posisiBarangModel = new PosisiBarangModel();
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
            case 'rusak': return 'danger';
            case 'hilang': return 'dark';
            default: return 'secondary';
        }
    }

    public function index()
    {
        $barang_details = $this->barangDetailModel
            ->select('barang_detail.*, barang.nama_barang, posisi_barang.nama_posisi, jenis_penggunaan.nama_penggunaan')
            ->join('barang', 'barang.id_barang = barang_detail.id_barang')
            ->join('posisi_barang', 'posisi_barang.id_posisi = barang_detail.id_posisi')
            ->join('jenis_penggunaan', 'jenis_penggunaan.id_penggunaan = barang_detail.id_jenis_penggunaan')
            ->findAll();

        foreach ($barang_details as &$barang) {
            $barang['status_class'] = $this->getStatusClass($barang['status']);
        }

        return view('barang-detail/index', ['barang_details' => $barang_details]);
    }

    public function create()
    {
        $data['barangs'] = $this->barangModel->getAvailableBarangForDetail();
        $data['posisi'] = $this->posisiBarangModel->findAll();
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

        // Jika jumlah Barang Detail sudah mencapai stok, tolak penyimpanan
        if ($jumlahDetail >= $stokBarang) {
            return redirect()->back()->with('error', 'Jumlah Barang Detail tidak boleh melebihi stok barang.');
        }

        // Validasi input
        if (!$this->validate([
            'id_barang' => 'required',
            'id_posisi' => 'required',
            'id_jenis_penggunaan' => 'required',
            'tahun_barang' => 'required',
            'serial_number' => 'permit_empty|is_unique[barang_detail.serial_number]',
            'nomor_bmn' => 'is_unique[barang_detail.nomor_bmn]|max_length[100]',
            'status' => 'required|in_list[tersedia,terpakai,dipinjam,rusak,hilang]',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data. Pastikan semua input benar dan Nomor BMN serta Serial Number tidak duplikat.');
        }

        // Simpan Barang Detail
        $this->barangDetailModel->insert([
            'id_barang' => $idBarang,
            'id_posisi' => $this->request->getPost('id_posisi'),
            'id_jenis_penggunaan' => $this->request->getPost('id_jenis_penggunaan'),
            'serial_number' => $this->request->getPost('serial_number') ?: null,
            'nomor_bmn' => $this->request->getPost('nomor_bmn'),
            'tahun_barang' => $this->request->getPost('tahun_barang'),
            'status' => $this->request->getPost('status'),
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
            'posisi' => $this->posisiBarangModel->findAll(),
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
            'id_posisi' => 'required',
            'id_jenis_penggunaan' => 'required',
            'tahun_barang' => 'required',
            'serial_number' => 'permit_empty|is_unique[barang_detail.serial_number,id_barang_detail,' . $id . ']',
            'nomor_bmn' => 'is_unique[barang_detail.nomor_bmn,id_barang_detail,' . $id . ']|max_length[100]',
            'status' => 'required|in_list[tersedia,terpakai,dipinjam,rusak,hilang]',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data. Pastikan semua input benar dan Nomor BMN serta Serial Number tidak duplikat.');
        }

        try {
            $this->barangDetailModel->update($id, [
                'id_barang' => $this->request->getPost('id_barang'),
                'id_posisi' => $this->request->getPost('id_posisi'),
                'id_jenis_penggunaan' => $this->request->getPost('id_jenis_penggunaan'),
                'serial_number' => $this->request->getPost('serial_number') ?: null,
                'nomor_bmn' => $this->request->getPost('nomor_bmn'),
                'tahun_barang' => $this->request->getPost('tahun_barang'),
                'status' => $this->request->getPost('status'),
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

}
