<?php

namespace App\Models\Workflow;

use CodeIgniter\Model;

class WorkflowEdgeModel extends Model
{
    protected $DBGroup       = 'gatepass';
    protected $table         = 'workflow_edges';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['workflow_template_id', 'from_node_id', 'to_node_id'];
    protected $returnType    = 'array';

    public function forTemplate(int $templateId): array
    {
        return $this->where('workflow_template_id', $templateId)->findAll();
    }

    /** Next node reached by following the single outgoing edge from $nodeId. Null = dead end. */
    public function nextNodeId(int $templateId, int $nodeId): ?int
    {
        $edge = $this->where('workflow_template_id', $templateId)
                     ->where('from_node_id', $nodeId)
                     ->first();
        return $edge ? (int) $edge['to_node_id'] : null;
    }
}
