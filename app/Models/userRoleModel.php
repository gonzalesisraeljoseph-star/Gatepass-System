<?php

namespace App\Models;

use CodeIgniter\Model;

class userRoleModel extends Model
{
    protected $DBGroup       = 'gatepass';
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

    /** Whether the HRIS employee/user id has an administrative role. */
    public function isAdministrator(int $userId): bool
    {
        if ($userId < 0) {
            return false;
        }

        $rows = $this->select('role.role_desc')
            ->join('role', 'role.role_id = user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->where('role.archived', 0)
            ->findAll();

        foreach ($rows as $row) {
            if (in_array(strtolower(trim($row['role_desc'])), ['admin', 'superadmin', 'super admin'], true)) {
                return true;
            }
        }

        return false;
    }
}
