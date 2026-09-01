<?php

namespace App\Models\Workflow;

use CodeIgniter\Model;

class WorkflowAssignmentModel extends Model
{
    protected $DBGroup       = 'gatepass';
    protected $table         = 'workflow_assignments';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['workflow_template_id', 'applies_to_type', 'applies_to_id', 'request_type'];
    protected $returnType    = 'array';

    /**
     * Find the template that applies to this requester for this request type.
     * A rule aimed at the specific user always wins over a rule aimed at any
     * one of their roles. No match at all => caller should auto-approve.
     *
     * NOTE the multi-role handling: your users can hold MULTIPLE roles via
     * role_module grants (that's the role_ids array from your session), so
     * this checks every role the user holds, not just one. If more than one
     * role-level rule matches, the first one found wins - tighten this with
     * a priority column on workflow_assignments later if you need
     * deterministic ordering between competing role rules.
     */
    public function resolveTemplateId(int $userId, array $roleIds, string $requestType): ?int
    {
        $userRule = $this->where('applies_to_type', 'user')
                          ->where('applies_to_id', $userId)
                          ->where('request_type', $requestType)
                          ->first();
        if ($userRule) {
            return (int) $userRule['workflow_template_id'];
        }

        if (empty($roleIds)) {
            return null;
        }

        $roleRule = $this->where('applies_to_type', 'role')
                          ->whereIn('applies_to_id', $roleIds)
                          ->where('request_type', $requestType)
                          ->first();

        return $roleRule ? (int) $roleRule['workflow_template_id'] : null;
    }
}
