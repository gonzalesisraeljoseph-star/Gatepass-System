<?php

namespace App\Models;

use CodeIgniter\Model;

class moduleModel extends Model
{
    protected $DBGroup = 'gatepass';
    protected $table         = 'modules';
    protected $primaryKey    = 'module_id';
    protected $allowedFields = ['module_name', 'archived'];
    protected $useTimestamps = false;

    /**
     * modules visible for a set of role_ids, straight from role_module -
     * a module only appears if role_module has a matching, non-archived
     * (role_id, module_id) row. No open-by-default fallback: if nothing's
     * granted, nothing shows.
     */
    public function forRoles(array $roleIds): array
    {
        if (empty($roleIds)) {
            return [];
        }

        return $this->select('modules.*')
            ->distinct()
            ->join('role_module', 'role_module.module_id = modules.module_id')
            ->whereIn('role_module.role_id', $roleIds)
            ->where('role_module.archived', 0)
            ->where('modules.archived', 0)
            ->orderBy('modules.module_id', 'asc')
            ->findAll();
    }

    /**
     * Same as forRoles(), with each module's visible sub_modules attached
     * under 'sub_modules' (empty array if it has none).
     */
    public function menuForRoles(array $roleIds): array
    {
        $modules       = $this->forRoles($roleIds);
        $subModuleModel = new subModuleModel();

        foreach ($modules as &$module) {
            $module['sub_modules'] = $subModuleModel->forRolesAndModule($roleIds, (int) $module['module_id']);
        }

        return $modules;
    }
}