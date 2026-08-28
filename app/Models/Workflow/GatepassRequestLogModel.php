<?php

namespace App\Models\Workflow;

use CodeIgniter\Model;

/**
 * Audit trail for workflow transitions (auto-approve, routed, floating,
 * approved_step, rejected, overridden).
 *
 * If you already have a general-purpose audit/activity log table for
 * gatepass requests, point $table at that instead of creating a parallel
 * one, and adjust $allowedFields to match its columns.
 */
class GatepassRequestLogModel extends Model
{
    protected $DBGroup       = 'gatepass';
    protected $table         = 'gatepass_request_logs';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['request_id', 'action', 'actor_id', 'notes', 'created_at'];
    protected $returnType    = 'array';
    protected $useTimestamps = false;
}
