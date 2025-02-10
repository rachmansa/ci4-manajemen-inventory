<?php

namespace App\Models;

use CodeIgniter\Model;

class SatuanBarangModel extends Model
{
    protected $table = 'satuan_barang';
    protected $primaryKey = 'id_satuan';
    protected $allowedFields = ['nama_satuan'];
    protected $useTimestamps = true;
}
