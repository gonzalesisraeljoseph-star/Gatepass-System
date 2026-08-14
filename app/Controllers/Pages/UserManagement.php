<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;
use App\Models\userManagementModel;
use Config\Database;

class UserManagement extends BaseController
{
    protected userManagementModel $userModel;

    public function __construct()
    {
        $this->userModel = new userManagementModel();
    }

    /**
     * Render the User Management page (table + create/edit modal).
     * The table itself is populated client-side via list().
     */
    public function index()
    {
        $data = [
            'users'       => $this->userModel->getAllUsersDetails(),
            'roles'       => $this->userModel->getRoles(),
            'departments' => $this->userModel->getDepartments(),
        ];

        return view('pages/usermanagement', $data);
    }

    /**
     * JSON feed for the DataTable (user-management/list, usermanagement/list).
     */
    public function list()
    {
        return $this->response->setJSON([
            'data' => $this->userModel->getAllUsersDetails(),
        ]);
    }

    /**
     * GET usermanagement/saveUser/(:num) - return a single user as JSON
     * (kept for the routed endpoint; the current UI populates the edit
     * modal client-side from the row data already in the table instead).
     */
    public function edituser($id)
    {
        $result = $this->userModel->editUser($id);

        if ($result === null) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }

        return $this->response->setJSON(['success' => true, 'user' => $result['user']]);
    }

    /**
     * POST usermanagement/saveUser | user-management/saveUser
     * Handles BOTH create and update - if 'id' is present in the POST
     * data, it's an update; otherwise it's a create.
     *
     * ref_emp is resolved by looking up the typed "Employee Name" against
     * v_profile_employee.full_name to get profile_id.
     *
     * email is intentionally ignored - no column exists for it.
     */
    public function saveUser()
    {
    $id              = $this->request->getPost('id');
    $employeeName    = trim((string) $this->request->getPost('employee'));
    $username        = $this->request->getPost('username');
    $password        = $this->request->getPost('password');
    $roleDescription = $this->request->getPost('role');
    $status          = $this->request->getPost('status'); // 'active' | 'inactive'

    // Resolve ref_emp from the typed employee name.
    $db = Database::connect(); // hris_system (default group)
    $employee = $db->table('v_profile_employee')
        ->select('profile_id')
        ->where('full_name', $employeeName)
        ->get()
        ->getRowArray();

    if (!$employee) {
        return $this->response->setJSON([
            'success' => false,
            'message' => "No employee found matching \"{$employeeName}\".",
        ]);
    }

    $refEmp = $employee['profile_id'];
    $flag   = $status === 'inactive' ? 0 : 1; // default to active if not sent

    if (!empty($id)) {
        if (empty($username)) {
            $existing = $this->userModel->find($id);
            if (!$existing) {
                return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
            }
            $username = $existing['username'];
        }

        $data = [
            'id'       => $id,
            'username' => $username,
            'ref_emp'  => $refEmp,
            'flag'     => $flag,
        ];

        if (!empty($password)) {
            $data['password'] = $password;
        }

        $success = $this->userModel->update($id, $data);
    } else {
        if (empty($username)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Username is required.']);
        }

        $data = [
            'username' => $username,
            'ref_emp'  => $refEmp,
            'flag'     => $flag,
        ];

        if (!empty($password)) {
            $data['password'] = $password;
        }

        $success = $this->userModel->insert($data);
    }

    if (!$success) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Validation failed.',
            'errors'  => $this->userModel->errors(),
        ]);
    }

    $roleResult = null;
    if (!empty($roleDescription)) {
        $roleResult = $this->userModel->assignRole($refEmp, $username, $roleDescription);
    }

    return $this->response->setJSON([
        'success'        => true,
        'role_submitted' => $roleDescription,
        'role_result'    => $roleResult,
    ]);
    }

    /**
     * POST usermanagement/deleteUser/(:num) | user-management/deleteUser/(:num)
     */
    public function toggleStatus($id)
    {   
    $userModel = new \App\Models\userManagementModel();
    $newFlag = $userModel->toggleFlag($id);

    if ($newFlag === null) {
        return redirect()->back()->with('error', 'User not found.');
    }

    return redirect()->back()->with(
        'success',
        $newFlag == 1 ? 'User activated.' : 'User deactivated.'
    );
    }

    /**
     * Manual/CLI-style backfill: bulk sync every user in tbl_user_info
     * into gatepass.users, and align user_roles.user_id for existing
     * role rows. Does NOT set role_id - only assignRole() does that.
     */
    public function syncUsers()
    {
        $result = $this->userModel->syncAllUsers();

        return redirect()->back()->with(
            'message',
            "Synced: {$result['synced']}, Skipped: {$result['skipped']}, Errors: " . count($result['errors'])
        );
    }
}