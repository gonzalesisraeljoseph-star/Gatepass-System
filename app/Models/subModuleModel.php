<?php

namespace App\Models;

use CodeIgniter\Model;

class subModuleModel extends Model
{
    protected $table         = 'sub_modules';
    protected $primaryKey    = 'sub_module_id';
    protected $allowedFields = ['module_id', 'sub_module_desc', 'archived'];
    protected $useTimestamps = false;

    /**
     * sub_modules visible under one module for a set of role_ids, straight
     * from role_submodule - a sub_module only appears if role_submodule
     * has a matching (role_id, module_id, sub_module_id) row.
     */
    public function forRolesAndModule(array $roleIds, int $moduleId): array
    {
        if (empty($roleIds)) {
            return [];
        }

        return $this->select('sub_modules.*')
            ->distinct()
            ->join('role_submodule', 'role_submodule.sub_module_id = sub_modules.sub_module_id')
            ->whereIn('role_submodule.role_id', $roleIds)
            ->where('role_submodule.module_id', $moduleId)
            ->where('role_submodule.archived', 0)
            ->where('sub_modules.archived', 0)
            ->orderBy('sub_modules.sub_module_id', 'asc')
            ->findAll();
    }
}