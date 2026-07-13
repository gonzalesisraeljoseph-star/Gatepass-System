<?php

namespace App\Controllers\Pages;
use App\Models\UM_Model;
use App\Controllers\BaseController;
use App\Models\userManagementModel;

class UserManagement extends BaseController
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
        return view('pages/usermanagement', [
            'title' => 'User Management'
        ]);
    }

    public function list()
    {
        $db = \Config\Database::connect();

        $data = $db->table('createuser c')
            ->select('c.*')
            ->orderBy('c.id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            "data" => $data
        ]);
    }

    public function newuser(){
        $data = [
            'employee' => 'John',
            'role' => 'employee'
        ];

        if($this ->request->getMethod() == 'post'){
            $model = new userManagementModel();
            $model -> save ($_POST);
        }
        return view('create_user', $data);

    }

    public function saveUser(){

    }
}