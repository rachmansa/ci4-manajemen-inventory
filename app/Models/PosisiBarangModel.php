<?php

namespace App\Models;

use CodeIgniter\Model;

class PosisiBarangModel extends Model
{
    protected $table = 'posisi_barang';
    protected $primaryKey = 'id_posisi';
    protected $allowedFields = ['nama_posisi'];
    protected $useTimestamps = true;
}
