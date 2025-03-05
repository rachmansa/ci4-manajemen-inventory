<?php

namespace App\Controllers;

use App\Models\BarangPegawaiUnitModel;
use App\Models\PegawaiUnitModel;
use App\Models\BarangModel;
use App\Models\BarangDetailModel;
use App\Models\LogPergerakanBarangModel;
use App\Models\JenisPenggunaanModel;
use CodeIgniter\Controller;

class BarangPegawaiUnitController extends Controller
{
    protected $barangPegawaiUnitModel;
    protected $pegawaiUnitModel;
    protected $barangModel;
    protected $barangDetailModel;
    protected $logPergerakanBarangModel;
    protected $jenisPenggunaanModel;
    protected $db;

    public function __construct()
    {
        $this->barangPegawaiUnitModel = new BarangPegawaiUnitModel();
        $this->pegawaiUnitModel = new PegawaiUnitModel();
        $this->barangModel = new BarangModel();
        $this->barangDetailModel = new BarangDetailModel(); 
        $this->logPergerakanBarangModel = new LogPergerakanBarangModel();
        $this->jenisPenggunaanModel = new JenisPenggunaanModel();
        $this->db = \Config\Database::connect(); // Inisialisasi database connection

    }

    public function index()
    {
        // $data['barangPegawaiUnits'] = $this->barangPegawaiUnitModel->findAll();
        // $data['pegawai_units'] = $this->pegawaiUnitModel->findAll();
        
        $data['barangPegawaiUnits'] = $this->barangPegawaiUnitModel->getAll();

        return view('barang-pegawai-unit/index', $data);
    }

 
    public function create($id_pegawai_unit)
    {
        $pegawai_unit = $this->pegawaiUnitModel->find($id_pegawai_unit);
        if (!$pegawai_unit) {
            return redirect()->to('barang-pegawai-unit')->with('error', 'Pegawai tidak ditemukan');
        }

        $data = [
            'pegawai_unit' => $pegawai_unit,
            'barang' => $this->barangModel->findAll(),
            'jenis_penggunaan' => $this->jenisPenggunaanModel->findAll(),
        ];

        return view('barang-pegawai-unit/create', $data);
    }



    public function store()
    {
        $validation = $this->validate([
            'id_barang' => 'required',
            'id_barang_detail' => 'required',
            'id_pegawai_unit' => 'required',
            'tanggal_serah_terima_awal' => 'required|valid_date',
            'kondisi_barang' => 'required',
        ]);
        
        if (!$validation) {
            log_message('error', print_r($this->validator->getErrors(), true));
            return redirect()->back()->withInput()->with('error', 'Data tidak valid');
        }

        $id_barang = $this->request->getPost('id_barang');
        $id_barang_detail = $this->request->getPost('id_barang_detail');
        $id_pegawai_unit = $this->request->getPost('id_pegawai_unit');
        $jumlah = 1;
        
        $barang = $this->barangModel->find($id_barang);
        $pegawai = $this->pegawaiUnitModel->find($id_pegawai_unit);
        $kondisi_barang = $this->request->getPost('kondisi_barang');

        if (!$barang || $barang['stok'] < $jumlah) {
            return redirect()->back()->withInput()->with('error', 'Stok barang tidak mencukupi');
        }

        if (!$pegawai) {
            return redirect()->back()->withInput()->with('error', 'Pegawai tidak ditemukan');
        }

        try {
            $barangPegawaiUnit = [
                'id_barang' => $id_barang,
                'id_barang_detail' => $id_barang_detail,
                'id_pegawai_unit' => $id_pegawai_unit,
                'id_jenis_penggunaan' => 3,
                'tanggal_serah_terima_awal' => $this->request->getPost('tanggal_serah_terima_awal'),
                'tanggal_serah_terima_akhir' => $this->request->getPost('tanggal_serah_terima_akhir'),
                'kondisi_barang' => $kondisi_barang,
                'keterangan' => $this->request->getPost('keterangan'),
            ];
            
            $this->barangPegawaiUnitModel->save($barangPegawaiUnit);

            // Update status barang_detail menjadi TERPAKAI & posisi barang
            $this->barangDetailModel->update($id_barang_detail, [
                'status' => 'terpakai',
                'posisi_barang' => "Barang Pegawai - {$pegawai['nama_pegawai']} "
            ]);
            
            $this->barangModel->update($id_barang, ['stok' => $barang['stok'] - $jumlah]);
            
            $this->logPergerakanBarangModel->tambahLog(
                $id_barang,
                $id_barang_detail,
                'Barang Pegawai Unit',
                'Penyimpanan Aset',
                "Disimpan oleh {$pegawai['nama_pegawai']}",
                'Barang disimpan sebagai Barang Pegawai'
            );
            
      
            return redirect()->to('/barang-pegawai-unit')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            log_message('error', 'Error saat menyimpan data: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        // $barangPegawaiUnit = $this->barangPegawaiUnitModel->find($id);
        $barangPegawaiUnit = $this->barangPegawaiUnitModel->where('id_barang_pegawai_unit', $id)->first();

        // log_message('debug', 'Data yang dikirim ke form edit: ' . json_encode($barangPegawaiUnit));
        log_message('debug', 'Data setelah query langsung: ' . json_encode($barangPegawaiUnit));


        if (!$barangPegawaiUnit) {
            return redirect()->to('barang-pegawai-unit')->with('error', 'Data tidak ditemukan');
        }

        // Ambil Barang Detail jika ada
        $barangDetail = null;
        if (!empty($barangPegawaiUnit['id_barang_detail'])) {
            $barangDetail = $this->barangDetailModel->find($barangPegawaiUnit['id_barang_detail']);
        }

        $barangDetails = [];
        if (!empty($barangPegawaiUnit['id_barang'])) {
            $barangDetails = $this->barangDetailModel->where('id_barang', $barangPegawaiUnit['id_barang'])->findAll();
        }

        $data = [
            'barangPegawaiUnit' => $barangPegawaiUnit,
            'jenis_penggunaan' => $this->jenisPenggunaanModel->find($barangPegawaiUnit['id_jenis_penggunaan']),
            'pegawai_unit' => $this->pegawaiUnitModel->find($barangPegawaiUnit['id_pegawai_unit']),
            'barang' => $this->barangModel->where('stok >', 0)->findAll(), // Hanya barang dengan stok > 0
            'barang_detail' => $barangDetail,
            'barang_details' => $barangDetails, // Barang detail berdasarkan barang yang dipilih sebelumnya
        ];
        return view('barang-pegawai-unit/edit', $data);
    }

    public function update($id)
    {
        $validation = $this->validate([
            'id_barang' => 'required',
            'id_barang_detail' => 'required',
            'id_pegawai_unit' => 'required',
            'kondisi_barang' => 'required',
            'tanggal_serah_terima_awal' => 'required|valid_date',
        ]);
        if (!$validation) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid');
        }
    
        try {
            $this->db->transBegin(); // **Mulai transaksi database**
    
            // **Cek apakah data barang pegawai unit yang akan diperbarui ada**
            $barangPegawaiUnitLama = $this->barangPegawaiUnitModel->find($id);
            if (!$barangPegawaiUnitLama) {
                return redirect()->to('/barang-pegawai-unit')->with('error', 'Data tidak ditemukan');
            }
            
    
            $id_barang_lama = $barangPegawaiUnitLama['id_barang'];
            $id_barang_detail_lama = $barangPegawaiUnitLama['id_barang_detail'];
            $id_barang_baru = $this->request->getPost('id_barang');
            $id_barang_detail_baru = $this->request->getPost('id_barang_detail');
            $id_pegawai_unit = $this->request->getPost('id_pegawai_unit');

            // Ambil data pegawai untuk update posisi barang
            $pegawai = $this->pegawaiUnitModel->find($id_pegawai_unit);
            if (!$pegawai) {
                return redirect()->back()->withInput()->with('error', 'Pegawai tidak ditemukan');
            }
            
    
            // **Cek apakah barang atau barang detail berubah**
            if ($id_barang_lama != $id_barang_baru || $id_barang_detail_lama != $id_barang_detail_baru) {
                // **Konfirmasi bahwa barang sebelumnya akan dikembalikan ke stok**
                session()->setFlashdata('warning', 'Barang sebelumnya akan dikembalikan ke stok');
    
                // **Jika barang lama tidak kosong, kembalikan stoknya**
                if ($id_barang_lama) {
                    $barangLama = $this->barangModel->find($id_barang_lama);
                    if ($barangLama) {
                        $this->barangModel->update($id_barang_lama, [
                            'stok' => $barangLama['stok'] + 1
                        ]);
                    }
                }
    
                // **Jika barang detail lama tidak kosong, ubah statusnya menjadi "Tersedia" dan posisinya ke "Penyimpanan Aset"**
                if ($id_barang_detail_lama) {
                    $barangDetailLama = $this->barangDetailModel->find($id_barang_detail_lama);
                    if ($barangDetailLama) {
                        $statusUpdate = [
                            'status' => 'Tersedia',
                            'posisi_barang' => 'Penyimpanan Aset' // **Menandakan barang dikembalikan ke penyimpanan**
                        ];

                        // **Jika barang lama dalam kondisi rusak, tetap kembalikan tetapi kondisi tetap rusak**
                        if ($barangDetailLama['kondisi'] == 'Rusak') {
                            $statusUpdate['kondisi'] = 'Rusak';
                        }

                        $this->barangDetailModel->update($id_barang_detail_lama, $statusUpdate);
                    }
                }

    
                // **Kurangi stok barang baru yang dipilih**
                if ($id_barang_baru) {
                    $barangBaru = $this->barangModel->find($id_barang_baru);
                    if ($barangBaru && $barangBaru['stok'] > 0) {
                        $this->barangModel->update($id_barang_baru, [
                            'stok' => $barangBaru['stok'] - 1
                        ]);
                    } else {
                        $this->db->transRollback(); // **Batalkan transaksi jika stok tidak mencukupi**
                        return redirect()->back()->withInput()->with('error', 'Stok barang tidak mencukupi');
                    }
                }
    
                // **Update status & posisi barang detail baru**
                if ($id_barang_detail_baru) {
                    $this->barangDetailModel->update($id_barang_detail_baru, [
                        'status' => 'Terpakai',
                        'posisi_barang' => "Barang Pegawai - {$pegawai['nama_pegawai']}"
                    ]);
                }

                // **Tambahkan log pergerakan barang**
                $this->logPergerakanBarangModel->insert([
                    'id_barang' => $id_barang_lama,
                    'id_barang_detail' => $id_barang_detail_lama,
                    'posisi_sebelumnya' => 'Pegawai Unit',
                    'posisi_sekarang' => 'Tersedia',
                    'keterangan' => 'Barang dikembalikan setelah diperbarui'
                ]);
    
                $this->logPergerakanBarangModel->insert([
                    'id_barang' => $id_barang_baru,
                    'id_barang_detail' => $id_barang_detail_baru,
                    'posisi_sebelumnya' => 'Tersedia',
                    'posisi_sekarang' => 'Pegawai Unit',
                    'keterangan' => 'Barang digunakan setelah diperbarui'
                ]);
            }
    
            // **Simpan perubahan ke database**
            $barangPegawaiUnit = [
                'id_barang' => $id_barang_baru,
                'id_barang_detail' => $id_barang_detail_baru,
                'id_pegawai_unit' => $id_pegawai_unit,
                'kondisi_barang' => $this->request->getPost('kondisi_barang'),
                'tanggal_serah_terima_awal' => $this->request->getPost('tanggal_serah_terima_awal'),
                'tanggal_serah_terima_akhir' => $this->request->getPost('tanggal_serah_terima_akhir'),
                'keterangan' => $this->request->getPost('keterangan'),
            ];
    
            $updated = $this->barangPegawaiUnitModel->update($id, $barangPegawaiUnit);
    
            // Jika kondisi_barang diubah menjadi "Rusak", update kondisi Barang Detail (baik lama maupun baru)
            if ($this->request->getPost('kondisi_barang') == 'rusak') {
                if ($id_barang_detail_lama) {
                    $updateLama = $this->barangDetailModel->update($id_barang_detail_lama, ['kondisi' => 'rusak']);
                    if (!$updateLama) {
                        log_message('error', 'Gagal update kondisi Rusak untuk Barang Detail Lama ID: ' . $id_barang_detail_lama);
                    }
                }

                if ($id_barang_detail_baru) {
                    $updateBaru = $this->barangDetailModel->update($id_barang_detail_baru, ['kondisi' => 'rusak']);
                    if (!$updateBaru) {
                        log_message('error', 'Gagal update kondisi Rusak untuk Barang Detail Baru ID: ' . $id_barang_detail_baru);
                    }
                }
            }


            if (!$updated) {
                log_message('error', 'Update Gagal: ' . json_encode($barangPegawaiUnit));
                $this->db->transRollback();
                return redirect()->back()->with('error', 'Update gagal dilakukan!');
            }
    
          

            $this->db->transCommit(); // **Simpan transaksi jika semua berhasil**
            return redirect()->to('/barang-pegawai-unit')->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            $this->db->transRollback(); // **Pastikan transaksi dibatalkan jika error**
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data');
        }
    }
    

    public function delete($id)
    {
        try {
            $this->db->transBegin(); // Mulai transaksi database

            // Cek apakah data barang pegawai unit yang akan dihapus ada
            $barangPegawaiUnit = $this->barangPegawaiUnitModel->find($id);
            if (!$barangPegawaiUnit) {
                $this->db->transRollback(); // Batalkan transaksi jika data tidak ditemukan
                return $this->response->setJSON(['error' => 'Data tidak ditemukan'])->setStatusCode(404);
            }
        
            $id_barang = $barangPegawaiUnit['id_barang'];
            $id_barang_detail = $barangPegawaiUnit['id_barang_detail'];

            // Kembalikan stok barang jika barang ada
            if ($id_barang) {
                $barang = $this->barangModel->find($id_barang);
                if ($barang) {
                    $stok_baru = $barang['stok'] + 1; // Pastikan stok bertambah dengan aman
                    $this->barangModel->update($id_barang, ['stok' => $stok_baru]);
                }
            }

            // Jika ada barang detail, ubah statusnya jadi "Tersedia" dan posisi jadi "Penyimpanan Aset"
            if ($id_barang_detail) {
                $barangDetail = $this->barangDetailModel->find($id_barang_detail);
                if ($barangDetail) {
                    $statusUpdate = [
                        'status' => 'Tersedia',
                        'posisi_barang' => 'Penyimpanan Aset'
                    ];

                    // Jika barang dalam kondisi rusak, kondisi tetap rusak
                    if ($barangPegawaiUnit['kondisi_barang'] == 'rusak') {
                        $statusUpdate['kondisi'] = 'rusak';
                    }

                    $this->barangDetailModel->update($id_barang_detail, $statusUpdate);
                }
            }

            // Tambahkan log pergerakan barang sebelum data dihapus
            $this->logPergerakanBarangModel->insert([
                'id_barang' => $id_barang,
                'id_barang_detail' => $id_barang_detail ?: null, // Pastikan bisa NULL
                'posisi_sebelumnya' => 'Pegawai Unit',
                'posisi_sekarang' => 'Penyimpanan Aset',
                'keterangan' => 'Barang dikembalikan setelah dihapus'
            ]);

            // Hapus data dari tabel barang pegawai unit
            // $this->barangPegawaiUnitModel->delete($id);
            $this->barangPegawaiUnitModel->delete($id, true);

            log_message('debug', 'Kondisi Barang Pegawai Unit: ' . json_encode($barangPegawaiUnit));

            $this->db->transCommit(); // Simpan transaksi jika semua berhasil
            
            // Simpan pesan sukses di session
            session()->setFlashdata('success', 'Data berhasil dihapus.');
            
            return $this->response->setJSON(['success' => 'Data berhasil dihapus'])->setStatusCode(200);
        
        } catch (\Exception $e) {
            $this->db->transRollback(); // Pastikan transaksi dibatalkan jika error
            return $this->response->setJSON(['error' => 'Terjadi kesalahan saat menghapus data'])->setStatusCode(500);
        }
    }


    

    public function getBarangDetail()
    {
        $id_barang = $this->request->getGet('id_barang');
        $barangDetailModel = new BarangDetailModel();

        $barangDetails = $barangDetailModel
            ->where('id_barang', $id_barang)
            ->where('id_jenis_penggunaan', 3) // Filter hanya untuk Barang Pegawai Unit
            ->where('status !=', 'Penghapusan Aset') // Hindari barang yang sudah dihapus
            ->where('status !=', 'Terpakai') // Hindari barang yang sudah terpakai
            ->where('kondisi !=', 'Hilang') // Hindari barang yang hilang
            ->findAll();

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


    public function getBarangByNip($nip)
    {
        $pegawai = $this->pegawaiUnitModel->where('nip', $nip)->first();
        
        if (!$pegawai) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Pegawai tidak ditemukan.']);
        }

        $barangList = $this->barangPegawaiUnitModel
            ->select('barang_pegawai_unit.*, barang.nama_barang, barang_detail.merk, barang_detail.serial_number, barang_detail.nomor_bmn, barang_detail.barcode')
            ->join('barang', 'barang.id_barang = barang_pegawai_unit.id_barang')
            ->join('barang_detail', 'barang_detail.id_barang_detail = barang_pegawai_unit.id_barang_detail', 'left')
            ->where('barang_pegawai_unit.id_pegawai_unit', $pegawai['id_pegawai_unit'])
            ->findAll();

        if (empty($barangList)) {
            return $this->response->setJSON(['status' => 'empty', 'message' => 'Pegawai ini belum memiliki barang.']);
        }

        // Buat tampilan HTML tabel dengan tombol Edit dan Delete
        $html = '';

        foreach ($barangList as $barang) {
            $html .= '<table class="table table-bordered mb-3">
                <tbody>
                    <tr>
                        <th>Nama Barang</th>
                        <td>' . esc($barang['nama_barang']) . '</td>
                    </tr>
                    <tr>
                        <th>Merk Barang</th>
                        <td>' . esc($barang['merk']) . '</td>
                    </tr>
                    <tr>
                        <th>Serial Number</th>
                        <td>' . (!empty($barang['serial_number']) ? esc($barang['serial_number']) : '-') . '</td>
                    </tr>
                    <tr>
                        <th>Nomor BMN</th>
                        <td>' . (!empty($barang['nomor_bmn']) ? esc($barang['nomor_bmn']) : '-') . '</td>
                    </tr>
                    <tr>
                        <th>Tanggal Serah Terima</th>
                        <td>
                            Awal: ' . (!empty($barang['tanggal_serah_terima_awal']) ? esc($barang['tanggal_serah_terima_awal']) : '-') . '<br>
                            Akhir: ' . (!empty($barang['tanggal_serah_terima_akhir']) ? esc($barang['tanggal_serah_terima_akhir']) : '-') . '
                        </td>
                    </tr>
                    <tr>
                        <th>Kondisi</th>
                        <td>' . esc($barang['kondisi_barang']) . '</td>
                    </tr>
                    <tr>
                        <th>Barcode</th>
                        <td>
                            <img src="'. base_url('barcode/generate/' . $barang['barcode']) . '" width="400">
                        </td>
                    </tr>
                    <tr>
                        <th>Aksi</th>
                        <td>
                            <a href="' . base_url('barang-pegawai-unit/edit/' . $barang['id_barang_pegawai_unit']) . '" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button class="btn btn-danger btn-sm deleteBarang" data-id="' . $barang['id_barang_pegawai_unit'] . '">
                                <i class="fa fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>';
        }
        
        
        return $this->response->setJSON(['status' => 'success', 'html' => $html]);
    }


}
