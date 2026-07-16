<?php

namespace App\Models;

use CodeIgniter\Model;

class userManagementModel extends Model
{
    protected $table            = 'tbl_user_info'; // lives in the 'default' group (hris_system)
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; // tbl_user_info has no deleted_at column
    protected $protectFields    = true;

    // Adjust these to match your actual `users` table columns.
    // NOTE: 'role' removed - tbl_user_info has no role column. Roles live
    // exclusively in the gatepass DB (user_roles.role_id) and are only ever
    // set via assignRole().
    protected $allowedFields = [
        'username',
        'md5_password',
        'ref_emp',
    ];

    // Timestamps
    // tbl_user_info has 'datetime_created' but no 'updated_at', so CI4's
    // built-in timestamp handling (which needs both) is disabled here.
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    // Validation
    protected $validationRules = [
        'id'       => 'permit_empty',
        'ref_emp'  => 'permit_empty',
        'username' => 'required|min_length[3]|max_length[100]|is_unique[tbl_user_info.username,id,{id}]',
        'password' => 'permit_empty|min_length[6]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    /**
     * Callbacks.
     *
     * beforeInsert / beforeUpdate: hash password, and (for updates) stash the
     * current ref_emp before it gets overwritten, so we can detect a change.
     *
     * afterInsert / afterUpdate: push the row into gatepass.users and keep
     * user_roles.user_id aligned with ref_emp. role_id is NEVER touched by
     * these callbacks - only assignRole() sets it.
     */
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword', 'stashOldRefEmp'];
    protected $afterInsert  = ['syncUserToGatepass'];
    protected $afterUpdate  = ['syncUserToGatepass'];

    /**
     * Holds ref_emp values captured just before an update, keyed by row id,
     * so afterUpdate can tell whether ref_emp changed.
     */
    private array $oldRefEmpMap = [];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && $data['data']['password'] !== '') {
            // Actual column is md5_password, not password.
            $data['data']['md5_password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }

        // Never let raw 'password' reach the DB - it's not a real column.
        unset($data['data']['password']);

        return $data;
    }

    /**
     * Capture the current ref_emp for each row about to be updated,
     * before the new values are written.
     */
    protected function stashOldRefEmp(array $data): array
    {
        if (empty($data['id'])) {
            return $data;
        }

        $ids = is_array($data['id']) ? $data['id'] : [$data['id']];

        foreach ($ids as $id) {
            $existing = $this->find($id);
            if ($existing) {
                $this->oldRefEmpMap[$id] = $existing['ref_emp'];
            }
        }

        return $data;
    }

    /**
     * Model callback: keep gatepass.users in sync, and migrate
     * user_roles.user_id if ref_emp changed. role_id is never touched here.
     */
    protected function syncUserToGatepass(array $data): array
    {
        $id = is_array($data['id']) ? $data['id'][0] : $data['id'];

        $row = $this->find($id);
        if (!$row || empty($row['ref_emp'])) {
            return $data;
        }

        $newRefEmp = $row['ref_emp'];
        $oldRefEmp = $this->oldRefEmpMap[$id] ?? null;

        $targetDb = \Config\Database::connect('gatepass');

        $exists = $targetDb->table('users')
            ->where('id', $newRefEmp)
            ->countAllResults();

        if ($exists) {
            $targetDb->table('users')
                ->where('id', $newRefEmp)
                ->update(['username' => $row['username']]);
        } else {
            $targetDb->query(
                'INSERT INTO users (id, username, created_at) VALUES (?, ?, NOW())',
                [$newRefEmp, $row['username']]
            );
        }

        // If ref_emp changed, migrate the existing user_roles row's user_id
        // to the new value. role_id, created_at stay untouched.
        if ($oldRefEmp && $oldRefEmp !== $newRefEmp) {
            $targetDb->table('user_roles')
                ->where('user_id', $oldRefEmp)
                ->update(['user_id' => $newRefEmp]);

            unset($this->oldRefEmpMap[$id]);
        }

        return $data;
    }

    /**
     * Get all users ordered by most recent first.
     */
    public function getAllUsersDetails()
    {
        $db = \Config\Database::connect(); // default group = hris_system

        $users = $db->table('tbl_user_info u')
            ->select("u.id, u.username, u.ref_emp,
                        e.full_name as employee,
                        e.department_name as department,
                        e.position_name as position,
                        '' as email,
                        'active' as status")
            ->join('v_profile_employee e', 'e.profile_id = u.ref_emp', 'left')
            ->orderBy('u.id', 'ASC')
            ->get()
            ->getResultArray();

        // Pull the actual assigned system role from the target (gatepass) DB,
        // keyed by ref_emp (which is what user_roles.user_id stores).
        $gatepassDb = \Config\Database::connect('gatepass');
        $roleRows = $gatepassDb->table('user_roles ur')
            ->select('ur.user_id, r.role_description')
            ->join('role r', 'r.role_id = ur.role_id')
            ->get()
            ->getResultArray();

        $roleMap = array_column($roleRows, 'role_description', 'user_id');

        foreach ($users as &$user) {
            $user['role'] = $roleMap[$user['ref_emp']] ?? '';
        }

        return $users;
    }

    /**
     * Get the list of departments.
     */
    public function getDepartments()
    {
        $db = \Config\Database::connect(); // default group = hris_system

        return $db->table('tbl_department')
            ->select('name as name')
            ->where('name !=', '')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get the list of roles.
     */
    public function getRoles()
    {
        $db = \Config\Database::connect('gatepass'); // target DB - role table lives here

        return $db->table('role')
            ->select('role_description as name')
            ->where('role_description !=', '')
            ->orderBy('role_description', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Assign a role to a user in the target (gatepass) database, keyed by
     * ref_emp. Updates the existing user_roles row's role_id if one exists,
     * otherwise inserts a new row (single-role model, matching the current
     * edit form's UI). This is the ONLY place role_id ever gets written.
     *
     * Returns a diagnostics array:
     *   success    - true if a role row now reflects the requested role_id
     *   action     - 'update' | 'insert' | 'role_not_found'
     *   user_id    - the ref_emp/user_id used to look up user_roles
     *   old_role_id - role_id before the change (null if row didn't exist)
     *   new_role_id - role_id that should now be set
     *   affected   - number of rows changed by the UPDATE query (0 means the
     *                WHERE clause matched nothing, even though the query ran)
     */
    public function assignRole(int $refEmp, string $username, string $roleDescription): array
    {
        $targetDb = \Config\Database::connect('gatepass');

        $role = $targetDb->table('role')
            ->where('role_description', $roleDescription)
            ->get()
            ->getRowArray();

        if (!$role) {
            return [
                'success'     => false,
                'action'      => 'role_not_found',
                'user_id'     => $refEmp,
                'old_role_id' => null,
                'new_role_id' => null,
                'affected'    => 0,
            ];
        }

        // Ensure the user exists in the target users table (FK requirement).
        $exists = $targetDb->table('users')->where('id', $refEmp)->countAllResults();
        if (!$exists) {
            $targetDb->query(
                'INSERT INTO users (id, username, created_at) VALUES (?, ?, NOW())',
                [$refEmp, $username]
            );
        } else {
            $targetDb->table('users')->where('id', $refEmp)->update(['username' => $username]);
        }

        // Single-role model: update role_id if a row already exists for this
        // user, otherwise insert a new one. Preserves created_at and row
        // identity instead of delete+insert on every role change.
        $existingRole = $targetDb->table('user_roles')
            ->where('user_id', $refEmp)
            ->get()
            ->getRowArray();

        if ($existingRole) {
            $targetDb->table('user_roles')
                ->where('user_id', $refEmp)
                ->update(['role_id' => $role['role_id']]); // updated_at auto-bumps via ON UPDATE CURRENT_TIMESTAMP

            $affected = $targetDb->affectedRows();

            return [
                'success'     => $affected > 0,
                'action'      => 'update',
                'user_id'     => $refEmp,
                'old_role_id' => $existingRole['role_id'],
                'new_role_id' => $role['role_id'],
                'affected'    => $affected,
            ];
        }

        $targetDb->query(
            'INSERT INTO user_roles (user_id, role_id, created_at) VALUES (?, ?, NOW())',
            [$refEmp, $role['role_id']]
        );

        return [
            'success'     => $targetDb->affectedRows() > 0,
            'action'      => 'insert',
            'user_id'     => $refEmp,
            'old_role_id' => null,
            'new_role_id' => $role['role_id'],
            'affected'    => $targetDb->affectedRows(),
        ];
    }

    /**
     * Bulk sync all users from tbl_user_info (hris_system) into the
     * gatepass database's `users` table, keyed by ref_emp. Also aligns
     * user_roles.user_id with ref_emp wherever a role row already exists.
     * role_id is NEVER set/changed here - only assignRole() does that.
     *
     * Intended as a manual/CLI backfill tool for rows that predate the
     * automatic afterInsert/afterUpdate hooks, or to catch drift.
     *
     * Returns ['synced' => int, 'skipped' => int, 'errors' => array]
     */
    public function syncAllUsers(): array
    {
        $db = \Config\Database::connect(); // hris_system
        $targetDb = \Config\Database::connect('gatepass');

        $sourceUsers = $db->table('tbl_user_info')
            ->select('id, username, ref_emp')
            ->get()
            ->getResultArray();

        $synced  = 0;
        $skipped = 0;
        $errors  = [];

        foreach ($sourceUsers as $u) {
            if (empty($u['ref_emp'])) {
                $skipped++;
                continue;
            }

            try {
                $exists = $targetDb->table('users')
                    ->where('id', $u['ref_emp'])
                    ->countAllResults();

                if ($exists) {
                    $targetDb->table('users')
                        ->where('id', $u['ref_emp'])
                        ->update(['username' => $u['username']]);
                } else {
                    $targetDb->query(
                        'INSERT INTO users (id, username, created_at) VALUES (?, ?, NOW())',
                        [$u['ref_emp'], $u['username']]
                    );
                }

                // Keep user_roles.user_id aligned with ref_emp wherever a
                // role row already exists. No-op if nothing to update, and
                // never touches role_id.
                $targetDb->table('user_roles')
                    ->where('user_id', $u['ref_emp'])
                    ->update(['user_id' => $u['ref_emp']]);

                $synced++;
            } catch (\Throwable $e) {
                $errors[] = [
                    'ref_emp'  => $u['ref_emp'],
                    'username' => $u['username'],
                    'error'    => $e->getMessage(),
                ];
            }
        }

        return [
            'synced'  => $synced,
            'skipped' => $skipped,
            'errors'  => $errors,
        ];
    }

    /**
     * Find a single user by username.
     */
    public function findByUsername(string $username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Build the data needed to render the edit-user form.
     * Returns null if the user isn't found - let the controller decide
     * how to handle that (e.g. redirect back with an error).
     */
    public function editUser($id)
    {
        $user = $this->find($id);

        if (!$user) {
            return null;
        }

        return [
            'user'       => $user,
            'role'       => $this->getRoles(),
            'department' => $this->getDepartments(),
        ];
    }
}