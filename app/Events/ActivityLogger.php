<?php

namespace App\Events;

use CodeIgniter\Events\Events;
use CodeIgniter\HTTP\IncomingRequest;
use Config\Services;
use App\Models\ActivityLogModel;

class ActivityLogger
{
    public static function logActivity()
    {
        $request = Services::request();
        $session = session();

        $data = [
            'user_id'    => $session->get('user_id') ?? null, // Sesuaikan dengan session user
            'method'     => $request->getMethod(),
            'endpoint'   => $request->getUri()->getPath(),
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent(),
        ];

        $logModel = new ActivityLogModel();
        $logModel->insert($data);
    }
}
