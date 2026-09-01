<?php

namespace App\Models\Workflow;

use CodeIgniter\Model;

/**
 * Ordered approver "slots" for a single workflow node. A node with rows
 * here is a multi-approve node: every slot must eventually be satisfied by
 * someone, tried in priority order, skipping whoever is unavailable at the
 * moment their slot comes up. A node with NO rows here uses the legacy
 * single role_id/user_id columns on workflow_nodes instead.
 */
class WorkflowNodeApproverModel extends Model
{
    protected $DBGroup       = 'gatepass';
    protected $table         = 'workflow_node_approvers';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['node_id', 'priority', 'role_id', 'user_id'];
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    /** All slots for a node, in the order they must be attempted. */
    public function forNode(int $nodeId): array
    {
        return $this->where('node_id', $nodeId)
                    ->orderBy('priority', 'ASC')
                    ->findAll();
    }
}
