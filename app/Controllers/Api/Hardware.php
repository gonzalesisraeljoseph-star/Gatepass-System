<?php
// app/Controllers/Api/Hardware.php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\HardwareApiService;
use App\Models\userManagementModel;

class Hardware extends BaseController
{
    /**
     * GET api/hardware
     *
     * Returns only the hardware assigned to the currently logged-in user.
     * Used to populate the device select on the gatepass request form.
     */
    public function getHardware()
    {
        if (! session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'status'  => false,
                'message' => 'Not logged in.',
                'rows'    => [],
            ]);
        }

        // Your session nests user data under 'logged_in', not a top-level
        // 'user_id' — that mismatch was why this previously returned
        // everyone's hardware instead of just the current user's.
        $sessionUser = session()->get('logged_in');
        $refEmp      = (int) ($sessionUser['ref_emp'] ?? 0);
        $username    = $sessionUser['username'] ?? '';
        $fullName    = $sessionUser['full_name'] ?? '';

        if (! $refEmp) {
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'No ref_emp found for current session user.',
                'rows'    => [],
            ]);
        }

        try {
            $userModel     = new userManagementModel();
            $snipeitUserId = $userModel->resolveSnipeitId($refEmp, $username, $fullName);

            if (! $snipeitUserId) {
                return $this->response->setJSON([
                    'status'  => false,
                    'message' => 'No matching Snipe-IT user found for this account.',
                    'rows'    => [],
                ]);
            }

            $service = new HardwareApiService();
            $rows    = $service->getHardwareByUser($snipeitUserId);

            return $this->response->setJSON([
                'status' => true,
                'rows'   => $rows,
            ]);

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