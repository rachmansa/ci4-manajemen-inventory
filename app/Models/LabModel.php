<?php

namespace App\Models;

use CodeIgniter\Model;

class LabModel extends Model
{
    protected $table = 'lab_cat';
    protected $primaryKey = 'id_lab';
    protected $allowedFields = ['nama_lab'];
    protected $useTimestamps = true;
}
