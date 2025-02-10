<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisBarangModel extends Model
{
    protected $table = 'jenis_barang';
    protected $primaryKey = 'id_jenis';
    protected $allowedFields = ['nama_jenis'];
    protected $useTimestamps = true;
}
