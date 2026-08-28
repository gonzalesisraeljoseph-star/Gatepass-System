<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;
use App\Libraries\Workflow\GatepassWorkflowEngine;

class Gatepass extends BaseController
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
        return view('pages/request');
    }

    public function store()
{
    $db = \Config\Database::connect('gatepass');

    $requestorId    = session()->get('logged_in')['ref_emp'];
    $reason         = $this->request->getPost('reason');
    $devices        = $this->request->getPost('devices');
    $requestor_name = $this->request->getPost('requestor_name');

    $db->table('gatepass_requests')->insert([
        'requestor_id'   => $requestorId,
        'reason'         => $reason,
        'requestor_name' => $requestor_name,
        'status'         => 'Pending'
    ]);

    $gatepassId = $db->insertID();

    if (!empty($devices)) {
        foreach ($devices as $deviceId) {
            $db->table('gatepass_request_items')->insert([
                'gatepass_id' => $gatepassId,
                'device_id'   => $deviceId
            ]);
        }
    }

    // Route the newly created request onto its workflow: resolves the
    // assigned template, finds the first approver (or auto-approves if
    // no workflow is wired), and inserts the pending approval row.
    (new GatepassWorkflowEngine())->route($gatepassId, (int) $requestorId, 'gatepass');

    return $this->response->setJSON([
        'status'  => true,
        'message' => 'Gatepass request submitted successfully'
    ]);
}

    public function list()
{
    $sessionData = session()->get('logged_in');
    $refEmp = $sessionData['ref_emp'] ?? null;

    if (!$refEmp) {
        return $this->response->setJSON([
            "status"  => false,
            "message" => "Not logged in"
        ])->setStatusCode(401);
    }

    $db = \Config\Database::connect('gatepass');

    $data = $db->table('gatepass_requests g')
        ->select('g.*')
        ->where('g.requestor_id', $refEmp)
        ->orderBy('g.id', 'DESC')
        ->get()
        ->getResultArray();

    return $this->response->setJSON([
        "data" => $data
    ]);
}
}