<?php
namespace App\Controllers;

use App\Models\BarangKeluarModel;
use App\Models\BarangModel;
use App\Models\BarangDetailModel;
use App\Models\JenisPenggunaanModel;
use CodeIgniter\Controller;

class BarangKeluarController extends Controller
{
    protected $barangKeluarModel;
    protected $barangModel;
    protected $barangDetailModel;
    protected $jenisPenggunaanModel;

    public function __construct()
    {
        $this->barangKeluarModel = new BarangKeluarModel();
        $this->barangModel = new BarangModel();
        $this->barangDetailModel = new BarangDetailModel();
    }

    public function index()
    {
        $data['barangKeluar'] = $this->barangKeluarModel->findAll();
        return view('barang-keluar/index', $data);
    }

    public function create()
    {
        $data = [
            'barang' => $this->barangModel->findAll(),
            'barangDetail' => $this->barangDetailModel->findAll(),
            'alasan' => ['Rusak', 'Habis Pakai', 'Dipindahkan'],
        ];

        return view('barang-keluar/create', $data);
    }

    public function store()
    {
        $data = [
            'id_barang' => $this->request->getPost('id_barang'),
            'id_barang_detail' => $this->request->getPost('id_barang_detail'),
            'jumlah' => $this->request->getPost('jumlah'),
            'tanggal_keluar' => $this->request->getPost('tanggal_keluar'),
            'alasan' => $this->request->getPost('alasan'),
            'pihak_penerima' => $this->request->getPost('pihak_penerima'),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        if ($this->barangKeluarModel->simpanBarangKeluar($data)) {
            return redirect()->to('/barang-keluar')->with('success', 'Barang berhasil dikeluarkan.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengeluarkan barang.');
        }
    }

    public function delete($id)
    {
        if ($this->barangKeluarModel->hapusBarangKeluar($id)) {
            return redirect()->to('/barang-keluar')->with('success', 'Barang Keluar berhasil dibatalkan.');
        } else {
            return redirect()->back()->with('error', 'Gagal membatalkan Barang Keluar.');
        }
    }

    public function getBarangDetail()
    {
        $id_barang = $this->request->getGet('id_barang');
        $barangDetailModel = new BarangDetailModel();

        $barangDetails = $barangDetailModel->where('id_barang', $id_barang)->findAll();

        $formattedDetails = array_map(function($detail) {
            return [
                'id_barang_detail' => $detail['id_barang_detail'],
                'nama_detail' => $detail['merk'] . ' - ' . 
                                (!empty($detail['serial_number']) ? $detail['serial_number'] : '(Tidak Ada)') . ' - ' . 
                                (!empty($detail['nomor_bmn']) ? $detail['nomor_bmn'] : '(Tidak Ada)')
            ];
        }, $barangDetails);

        return $this->response->setJSON($formattedDetails);
    }


}