<?php
namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table      = 'activity_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'method', 'endpoint', 'ip_address', 'user_agent', 'created_at'];

    public function getLogsWithUserName()
    {
        return $this->select('activity_logs.*, users.username')
                    ->join('users', 'users.id = activity_logs.user_id', 'left')
                    ->orderBy('activity_logs.created_at', 'DESC')
                    ->findAll();
    }
}
