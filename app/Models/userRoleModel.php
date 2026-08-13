<?php

namespace App\Models;

use CodeIgniter\Model;

class userRoleModel extends Model
{
    protected $table         = 'user_roles';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['user_id', 'role_id'];
    protected $useTimestamps = true; // table has created_at / updated_at

    /**
     * A user can have more than one role_id (user_roles is a plain
     * many-to-many junction: id, user_id, role_id) - return all of them.
     *
     * user_id here is ref_emp (the HRIS employee id from tbl_user_info /
     * v_profile_employee), matching how the rest of the app scopes by
     * session('logged_in')['ref_emp'] - not a local users.id.
     */
    public function roleIdsForUser(int $refEmp): array
    {
        $rows = $this->select('role_id')->where('user_id', $refEmp)->findAll();

        return array_map(static fn ($r) => (int) $r['role_id'], $rows);
    }
}