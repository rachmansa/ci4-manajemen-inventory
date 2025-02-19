<?php

namespace App\Controllers;

use App\Models\BarangKeluarModel;
use App\Models\BarangModel;
use App\Models\BarangDetailModel;
use CodeIgniter\Controller;

class BarangKeluarController extends Controller
{
    protected $barangKeluarModel;
    protected $barangModel;
    protected $barangDetailModel;

    public function __construct()
    {
        $this->barangKeluarModel = new BarangKeluarModel();
        $this->barangModel = new BarangModel();
        $this->barangDetailModel = new BarangDetailModel();
    }

    public function index()
    {
        $data['barang_keluar'] = $this->barangKeluarModel->getBarangKeluar();
        // dd($data);
        return view('barang-keluar/index', $data);
    }

    public function create()
    {
        $data = [
            'barang' => $this->barangModel->findAll(),
            'alasan' => ['Rusak', 'Habis Pakai', 'Dipindahkan'],
        ];
        return view('barang-keluar/create', $data);
    }

    public function store()
    {
        $rules = [
            'id_barang' => 'required|integer',
            'jumlah' => 'required|integer|greater_than[0]',
            'tanggal_keluar' => 'required|valid_date[Y-m-d]',
            'alasan' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid');
        }

        $id_barang = $this->request->getPost('id_barang');
        $id_barang_detail = $this->request->getPost('id_barang_detail') ?? [];
        $jumlah = (int) $this->request->getPost('jumlah');

        // Kurangi stok barang
        if (!$this->barangModel->kurangiStok($id_barang, $jumlah)) {
            return redirect()->back()->withInput()->with('error', 'Stok tidak mencukupi!');
        }

        // Simpan ke database
        $this->barangKeluarModel->insert([
            'id_barang' => $id_barang,
            'id_barang_detail' => implode(',', $this->request->getPost('id_barang_detail') ?? []), // Simpan sebagai string (1,2,3)
            'jumlah' => $jumlah,
            'tanggal_keluar' => $this->request->getPost('tanggal_keluar'),
            'alasan' => $this->request->getPost('alasan'),
            'pihak_penerima' => $this->request->getPost('pihak_penerima'),
            'keterangan' => $this->request->getPost('keterangan'),

        ]);

        // Update status barang detail ke "Penghapusan Aset"
        if (!empty($id_barang_detail)) {
            $this->barangKeluarModel->updateStatusBarangDetail($id_barang_detail);
        }

        


        return redirect()->to('/barang-keluar')->with('success', 'Barang berhasil dikeluarkan!');
    }

    // public function edit($id)
    // {
    //     $barang_keluar = $this->barangKeluarModel->find($id);
    //     if (!$barang_keluar) {
    //         return redirect()->to('/barang-keluar')->with('error', 'Data tidak ditemukan!');
    //     }

    //     // Ambil barang terkait
    //     $barang = $this->barangModel->findAll();

    //     // Ambil barang detail terkait jika ada
    //     $barang_detail = [];
    //     if (!empty($barang_keluar['id_barang_detail'])) {
    //         $barang_detail_ids = explode(',', $barang_keluar['id_barang_detail']);
    //         $barang_detail = $this->barangDetailModel->whereIn('id_barang_detail', $barang_detail_ids)->findAll();
    //     }

    //     $data = [
    //         'barang_keluar' => $barang_keluar,
    //         'barang' => $barang,
    //         'barang_detail' => $barang_detail,
    //         'alasan' => ['Rusak', 'Habis Pakai', 'Dipindahkan'],
    //     ];

    //     return view('barang-keluar/edit', $data);
    // }

    public function edit($id)
    {
        $barang_keluar = $this->barangKeluarModel->find($id);
        if (!$barang_keluar) {
            return redirect()->to('/barang-keluar')->with('error', 'Data tidak ditemukan!');
        }

        // Ambil data barang terkait
        $barang = $this->barangModel->find($barang_keluar['id_barang']);

        // Ambil data barang detail jika ada
        $barang_detail = $this->barangDetailModel->where('id_barang', $barang_keluar['id_barang'])->findAll();

        $data = [
            'barang_keluar' => $barang_keluar,
            'barang' => $barang, // Data barang terkait
            'barang_detail' => $barang_detail, // Data barang detail
            'alasan' => ['Rusak', 'Habis Pakai', 'Dipindahkan'],
        ];

        return view('barang-keluar/edit', $data);
    }


    // public function update($id)
    // {
    //     $rules = [
    //         'id_barang' => 'required|integer',
    //         'jumlah' => 'required|integer|greater_than[0]',
    //         'tanggal_keluar' => 'required|valid_date[Y-m-d]',
    //         'alasan' => 'required',
    //     ];

    //     if (!$this->validate($rules)) {
    //         return redirect()->back()->withInput()->with('error', 'Data tidak valid');
    //     }

    //     $barang_keluar = $this->barangKeluarModel->find($id);
    //     if (!$barang_keluar) {
    //         return redirect()->to('/barang-keluar')->with('error', 'Data tidak ditemukan!');
    //     }

    //     $id_barang = $this->request->getPost('id_barang');
    //     $id_barang_detail = $this->request->getPost('id_barang_detail') ?? [];
    //     $jumlah_baru = count($id_barang_detail) > 0 ? count($id_barang_detail) : (int) $this->request->getPost('jumlah');

    //     // Kembalikan stok lama sebelum update
    //     if ($barang_keluar['jumlah'] !== $jumlah_baru) {
    //         $this->barangModel->tambahStok($barang_keluar['id_barang'], $barang_keluar['jumlah']);
    //         if (!$this->barangModel->kurangiStok($id_barang, $jumlah_baru)) {
    //             return redirect()->back()->with('error', 'Stok tidak mencukupi!');
    //         }
    //     }

    //     // Perbarui data barang keluar
    //     $this->barangKeluarModel->update($id, [
    //         'id_barang' => $id_barang,
    //         'id_barang_detail' => implode(',', $id_barang_detail),
    //         'jumlah' => $jumlah_baru,
    //         'tanggal_keluar' => $this->request->getPost('tanggal_keluar'),
    //         'alasan' => $this->request->getPost('alasan'),
    //         'pihak_penerima' => $this->request->getPost('pihak_penerima'),
    //     ]);

    //     // Update status barang detail ke "Penghapusan Aset"
    //     if (!empty($id_barang_detail)) {
    //         $this->barangKeluarModel->updateStatusBarangDetail($id_barang_detail, $this->request->getPost('kondisi'));
    //     }


    //     return redirect()->to('/barang-keluar')->with('success', 'Barang Keluar berhasil diperbarui!');
    // }

    public function update($id)
    {
        $rules = [
            'id_barang' => 'required|integer',
            'jumlah' => 'required|integer|greater_than[0]',
            'tanggal_keluar' => 'required|valid_date[Y-m-d]',
            'alasan' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid');
        }

        $barang_keluar = $this->barangKeluarModel->find($id);
        if (!$barang_keluar) {
            return redirect()->to('/barang-keluar')->with('error', 'Data tidak ditemukan!');
        }

        $id_barang = $this->request->getPost('id_barang');
        $id_barang_detail_baru = $this->request->getPost('id_barang_detail') ?? [];
        $jumlah_baru = count($id_barang_detail_baru) > 0 ? count($id_barang_detail_baru) : (int) $this->request->getPost('jumlah');

        // Kembalikan stok lama sebelum update
        if ($barang_keluar['jumlah'] !== $jumlah_baru) {
            $this->barangModel->tambahStok($barang_keluar['id_barang'], $barang_keluar['jumlah']);
            if (!$this->barangModel->kurangiStok($id_barang, $jumlah_baru)) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi!');
            }
        }

        // Dapatkan id_barang_detail lama
        $id_barang_detail_lama = explode(',', $barang_keluar['id_barang_detail']);

        // Simpan status sebelumnya dari barang_detail lama
        $status_sebelumnya = [];
        foreach ($id_barang_detail_lama as $id_detail) {
            $barang_detail = $this->barangDetailModel->find($id_detail);
            if ($barang_detail) {
                $status_sebelumnya[$id_detail] = $barang_detail['status'];
            }
        }

        // Perbarui data barang keluar
        $this->barangKeluarModel->update($id, [
            'id_barang' => $id_barang,
            'id_barang_detail' => implode(',', $id_barang_detail_baru),
            'jumlah' => $jumlah_baru,
            'tanggal_keluar' => $this->request->getPost('tanggal_keluar'),
            'alasan' => $this->request->getPost('alasan'),
            'pihak_penerima' => $this->request->getPost('pihak_penerima'),
        ]);

        // Kembalikan status barang_detail yang tidak disertakan
        foreach ($id_barang_detail_lama as $id_detail) {
            if (!in_array($id_detail, $id_barang_detail_baru)) {
                // Kembalikan status ke status sebelumnya
                if (isset($status_sebelumnya[$id_detail])) {
                    $this->barangDetailModel->update($id_detail, ['status' => $status_sebelumnya[$id_detail]]);
                }
            }
        }

        // Update status barang_detail baru ke "Penghapusan Aset"
        if (!empty($id_barang_detail_baru)) {
            $this->barangKeluarModel->updateStatusBarangDetail($id_barang_detail_baru, $this->request->getPost('kondisi'));
        }

        // Hapus barang dari Barang Lab & Barang Pegawai jika masih ada
        if (!empty($id_barang_detail_baru)) {
            $this->barangKeluarModel->hapusDariLabDanPegawai($id_barang_detail);
        }

        return redirect()->to('/barang-keluar')->with('success', 'Barang Keluar berhasil diperbarui!');
    }


    
    public function delete($id)
    {
        $barang_keluar = $this->barangKeluarModel->find($id);
        if (!$barang_keluar) {
            return redirect()->to('/barang-keluar')->with('error', 'Data tidak ditemukan!');
        }

        // Kembalikan stok sebelum hapus
        $this->barangModel->tambahStok($barang_keluar['id_barang'], $barang_keluar['jumlah']);

        $this->barangKeluarModel->delete($id);
        return redirect()->to('/barang-keluar')->with('success', 'Barang Keluar berhasil dihapus!');
    }


    public function getBarangDetail($id_barang)
    {
        $barangDetailModel = new BarangDetailModel();
        $data = $this->barangKeluarModel->getBarangDetailTersedia($id_barang);

        $formattedData = array_map(function ($item) {
            return [
                'id_barang_detail' => $item['id_barang_detail'],
                'serial_number'    => $item['serial_number'] ?: 'Tanpa SN',
                'nomor_bmn'        => $item['nomor_bmn'] ?: 'Tanpa BMN'
            ];
        }, $data);

        return $this->response->setJSON($formattedData);
    }





   



}
