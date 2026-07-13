<?php

namespace App\Controllers\Pages;
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
        $model = new userManagementModel();

        return view('pages/usermanagement', [
            'title' => 'User Management',
            'users' => $model->getAllUsersDetails(),
            'departments' => $model->getDepartments(),
            'roles' => $model->getRoles()
        ]);
    }

    public function list()
    {
        $model = new userManagementModel();
        $data = $model->getAllUsersDetails();

        return $this->response->setJSON([
            'data' => $data
        ]);
    }

    


    public function edituser(){
        $data = [
            'user' => '$user',
            'role' => '$role'
        ];

        if($this ->request->getMethod() == 'post'){
            $model = new userManagementModel();
            $model -> save ($_POST);
        }
        return view('create_user', $data);

    }

    public function saveUser()
    {
        $model = new userManagementModel();
        $post = $this->request->getPost();
        $id = $post['id'] ?? null;

        $db = \Config\Database::connect();
        $tableFields = array_map('strtolower', $db->getFieldNames($model->table));
        $data = [];

        if (in_array('username', $tableFields, true)) {
            $data['username'] = $post['username'] ?? '';
        }

        if (!empty($post['password']) && in_array('md5_password', $tableFields, true)) {
            $data['md5_password'] = md5($post['password']);
        }

        if (!empty($id)) {
            $db->table($model->table)->where('id', $id)->update($data);
        } else {
            $db->table($model->table)->insert($data);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'User saved successfully.'
        ]);
    }

    public function deleteUser($id = null)
    {
        if (empty($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User id is required.'
            ]);
        }

        $model = new userManagementModel();
        $user = $model->find($id);

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }

        $model->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'User deleted successfully.'
        ]);
    }

      public function listUser($id){

        $model = new userManagementModel();
        $user  = $model->find($id);
        if($user){
            $data = [
                'user' => $user
            ];
            return view('users/show', $data);
        }

        return redirect()->to('users')->with('error', 'User not found.');
    }
}