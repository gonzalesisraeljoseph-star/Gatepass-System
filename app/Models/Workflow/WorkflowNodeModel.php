<?php

namespace App\Models\Workflow;

use CodeIgniter\Model;

class WorkflowNodeModel extends Model
{
    protected $DBGroup       = 'gatepass';
    protected $table         = 'workflow_nodes';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'workflow_template_id', 'node_type', 'role_id', 'user_id', 'label', 'pos_x', 'pos_y',
    ];
    protected $returnType = 'array';

    public function forTemplate(int $templateId): array
    {
        return $this->where('workflow_template_id', $templateId)->findAll();
    }

    public function startNode(int $templateId): ?array
    {
        return $this->where('workflow_template_id', $templateId)
                    ->where('node_type', 'start')
                    ->first();
    }
}
