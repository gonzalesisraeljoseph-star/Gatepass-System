<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;
use App\Libraries\HardwareApiService;
use App\Models\userManagementModel;

class Accessories extends BaseController
{
    public function __construct()
    {
        if (!session()->get('logged_in')) {
            header('Location: ' . base_url('/'));
            exit;
        }
    }

    public function index()
    {
        $hardware = [];
        $error = null;

        try {
            $sessionUser = session()->get('logged_in');

            $refEmp   = (int) ($sessionUser['ref_emp'] ?? 0);
            $username = $sessionUser['username'] ?? '';
            $fullName = $sessionUser['full_name'] ?? '';

            if (! $refEmp) {
                throw new \RuntimeException('No ref_emp found in session for the current user.');
            }

            $userModel = new userManagementModel();

            // Persisted lookup: checks gatepass.users.snipeit_id first,
            // only hits the Snipe-IT API if it hasn't been resolved before.
            $snipeitUserId = $userModel->resolveSnipeitId($refEmp, $username, $fullName);

            if (! $snipeitUserId) {
                throw new \RuntimeException(
                    "No matching Snipe-IT user found (ref_emp: {$refEmp}, username: {$username}, name: {$fullName})"
                );
            }

            $service  = new HardwareApiService();
            $hardware = $service->getHardwareByUser($snipeitUserId);

        } catch (\Throwable $e) {
            log_message('error', 'HardwareApiService error: ' . $e->getMessage());
            $error = 'Unable to load accessories right now.';
        }

        return view('pages/accessories', [
            'title'    => 'Accessories',
            'hardware' => $hardware,
            'error'    => $error,
        ]);
    }
}