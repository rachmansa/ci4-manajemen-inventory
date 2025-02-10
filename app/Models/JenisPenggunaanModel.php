<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisPenggunaanModel extends Model
{
    protected $table = 'jenis_penggunaan';
    protected $primaryKey = 'id_penggunaan';
    protected $allowedFields = ['nama_penggunaan'];
    protected $useTimestamps = true;
}
