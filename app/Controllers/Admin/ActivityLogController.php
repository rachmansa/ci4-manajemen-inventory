<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
use App\Models\UserModel;

class ActivityLogController extends BaseController
{
    public function index()
    {
        $logModel = new ActivityLogModel();
        $userModel = new UserModel();

        // Ambil filter dari input GET
        $userId = $this->request->getGet('user_id');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        // Query log dengan filter
        $query = $logModel->orderBy('created_at', 'DESC');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($startDate && $endDate) {
            $query->where('created_at >=', $startDate . ' 00:00:00');
            $query->where('created_at <=', $endDate . ' 23:59:59');
        }

        // Ambil data logs dengan username
        $data['logs'] = $query->getLogsWithUserName();
        $data['users'] = $userModel->findAll(); // Ambil daftar user untuk dropdown filter

        return view('admin/activity_logs', $data);
    }
}
