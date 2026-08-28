<?php

namespace App\Models\Workflow;

use CodeIgniter\Model;

class WorkflowTemplateModel extends Model
{
    protected $DBGroup       = 'gatepass'; // adjust if your group name differs
    protected $table         = 'workflow_templates';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name', 'description', 'request_type', 'status', 'created_by', 'created_at', 'updated_at',
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = false;
}
