<?php

namespace App\Libraries\Workflow;

use Config\Database;

/**
 * Resolves people <-> roles for the workflow engine, matched exactly to
 * your real db_gatepass schema (from db_gatepass__3_.sql):
 *
 *   role        (role_id, role_desc, archived)
 *   users       (id, username, snipeit_id, created_at)   -- no name/status/availability columns
 *   user_roles  (id, user_id, role_id, created_at, updated_at)
 *
 * Everything lives in ONE database (db_gatepass) - no hris_system split
 * needed for role resolution, unlike my first guess.
 *
 * TWO COLUMNS YOUR SCHEMA DOESN'T HAVE YET, which this engine needs:
 *   - users.is_available   (for the "TL is on leave -> float" rule)
 *   - role.can_override    (for who may force-close a floating/pending request)
 * The companion migration 2026-08-18-000003_AddWorkflowResolverColumns.php
 * adds both, defaulting can_override=1 on the 'Admin' role (role_id 5 in
 * your seed data) so override capability works out of the box. Adjust the
 * seed UPDATE in that migration if Admin isn't role_id 5 in your live DB.
 */
class GatepassRoleResolver
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect('gatepass'); // ADJUST group name if your Config\Database alias differs
    }

    /** All role IDs a given user currently holds (usually just one, per your seed data). */
    public function getRoleIdsForUser(int $userId): array
    {
        $rows = $this->db->table('user_roles')
                          ->select('role_id')
                          ->where('user_id', $userId)
                          ->get()->getResultArray();

        return array_map(fn ($r) => (int) $r['role_id'], $rows);
    }

    /**
     * Active + available users holding a given role - resolves a role-bound
     * approver node to an actual person.
     * Filters out archived roles and (once the migration runs) unavailable
     * users. Until the migration runs, is_available is treated as always 1.
     */
    public function availableForRole(int $roleId): array
    {
        $hasAvailabilityCol = $this->db->fieldExists('is_available', 'users');

        $builder = $this->db->table('user_roles ur')
                             ->select('u.*')
                             ->join('users u', 'u.id = ur.user_id')
                             ->join('role r', 'r.role_id = ur.role_id')
                             ->where('ur.role_id', $roleId)
                             ->where('r.archived', 0);

        if ($hasAvailabilityCol) {
            $builder->where('u.is_available', 1);
        }

        return $builder->get()->getResultArray();
    }

    /** True if a specific person can currently act as approver. */
    public function isAvailable(int $userId): bool
    {
        $u = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        if (!$u) {
            return false;
        }
        // is_available only exists after the companion migration runs; treat
        // its absence as "everyone available" so this doesn't hard-fail pre-migration.
        return !array_key_exists('is_available', $u) || (int) $u['is_available'] === 1;
    }

    /** True if this user holds a role flagged can_override (needs the companion migration). */
    public function isOverrideCapable(int $userId): bool
    {
        if (!$this->db->fieldExists('can_override', 'role')) {
            return false; // migration not run yet - nobody can override
        }

        $row = $this->db->table('user_roles ur')
                         ->join('role r', 'r.role_id = ur.role_id')
                         ->where('ur.user_id', $userId)
                         ->where('r.can_override', 1)
                         ->get()->getRowArray();

        return (bool) $row;
    }
}
