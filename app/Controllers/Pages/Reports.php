<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;
use App\Models\userRoleModel;
use Config\Database;

class Reports extends BaseController
{
    public function index()
    {
        $sessionUser = session()->get('logged_in');
        if (!$sessionUser) {
            return redirect()->to('/');
        }

        $refEmp = (int) ($sessionUser['ref_emp'] ?? 0);
        $isAdmin = (new userRoleModel())->isAdministrator($refEmp);
        if ($refEmp <= 0 && !$isAdmin) {
            return redirect()->to('dashboard');
        }

        $query = Database::connect('gatepass')->table('gatepass_requests')
            ->select('id, requestor_name, reason, status, created_at')
            ->orderBy('id', 'DESC');
        if (!$isAdmin) {
            $query->where('requestor_id', $refEmp);
        }

        $requests = $query->get()->getResultArray();
        $statusCounts = [];
        foreach ($requests as $request) {
            $status = $request['status'] ?: 'Unknown';
            $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
        }

        return view('pages/reports', [
            'requests'     => $requests,
            'statusCounts' => $statusCounts,
            'isAdmin'      => $isAdmin,
        ]);
    }
}
