<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserRoleModel;
use Firebase\JWT\JWT;

class Auth extends BaseController
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }


    public function index()
    {
        echo view('auth/header');
        echo view('auth/index');
        echo view('auth/footer');
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        if (empty($username) || empty($password)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Username and password required'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();

        $user = $db->table('tbl_user_info u')
            ->select('
                u.id,
                u.username,
                u.ref_emp,
                u.md5_password,
                e.full_name,
                e.firstname,
                e.lastname,
                e.middlename,
                e.position_name,
                e.department_name
            ')
            ->join('v_profile_employee e', 'e.profile_id = u.ref_emp', 'left')
            ->where('u.username', $username)
            ->get()
            ->getRow();

        if (!$user || $user->md5_password !== md5($password)) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Invalid credentials'
            ])->setStatusCode(401);
        }

        // RBAC: role_ids come from db_gatepass.user_roles, keyed by ref_emp
        // (same identifier the rest of the app already scopes by), not the
        // local `users` table (that one's just the Snipe-IT id cache).
        $roleIds = (new UserRoleModel())->roleIdsForUser((int) $user->ref_emp);

       $this->session->set('logged_in', [
            'ref_emp'         => $user->ref_emp,
            'username'        => $user->username,
            'full_name'       => $user->full_name,
            'position_name'   => $user->position_name,
            'department_name' => $user->department_name,
            'role_ids'        => $roleIds,
        ]);

        $key  = getenv('JWT_SECRET');
        $time = time();

        $payload = [
            'iat'             => $time,
            'exp'             => $time + (int) getenv('JWT_EXPIRATION'),
            'uid'             => $user->id,
            'ref_emp'         => $user->ref_emp,
            'username'        => $user->username,
            'full_name'       => $user->full_name,
            'position_name'   => $user->position_name,
            'department_name' => $user->department_name,
        ];

        $token = JWT::encode($payload, $key, 'HS256');

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => [
                'ref_emp'   => $user->ref_emp,
                'username'  => $user->username,
                'full_name' => $user->full_name

            ]
        ]);
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('/');
    }
}