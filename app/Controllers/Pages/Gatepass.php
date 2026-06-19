<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;

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
        $db = \Config\Database::connect();

        $requestorId        = session()->get('logged_in')['ref_emp'];
        $reason             = $this->request->getPost('reason');
        $devices            = $this->request->getPost('devices');
        $requestor_name     = $this->request->getPost('requestor_name');

        $db->table('gatepass_requests')->insert([
            'requestor_id'      => $requestorId,
            'reason'            => $reason,
            'requestor_name'    => $requestor_name,
            'status'            => 'Pending'
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

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Gatepass request submitted successfully'
        ]);
    }

    public function list()
    {
        $db = \Config\Database::connect();

        $data = $db->table('gatepass_requests g')
            ->select('g.*')
            ->orderBy('g.id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            "data" => $data
        ]);
    }
}