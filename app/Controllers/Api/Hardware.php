<?php
// app/Controllers/Api/Hardware.php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\HardwareApiService;

class Hardware extends BaseController
{
    public function getHardware()
    {
        $userId = session()->get('user_id');

        try {
            $service = new HardwareApiService();
            $result  = $service->getHardwareRaw($userId);

            return $this->response->setJSON($result);

        } catch (\Throwable $e) {
            log_message('error', 'HardwareApiService error: ' . $e->getMessage());

            return $this->response->setJSON([
                'status'  => false,
                'message' => 'Unable to reach hardware service.',
                'rows'    => [],
            ])->setStatusCode(502);
        }
    }
}