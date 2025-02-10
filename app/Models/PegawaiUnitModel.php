<?php

namespace App\Models;

use CodeIgniter\Model;

class PegawaiUnitModel extends Model
{
    protected $table = 'pegawai_unit';
    protected $primaryKey = 'id_pegawai_unit';
    protected $allowedFields = ['nip', 'nama_pegawai', 'unit_kerja'];
    protected $useTimestamps = true;
}
