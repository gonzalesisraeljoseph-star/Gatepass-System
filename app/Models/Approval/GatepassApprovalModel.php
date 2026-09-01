<?php

namespace App\Models\Approval;

use CodeIgniter\Model;

class GatepassApprovalModel extends Model
{
    protected $DBGroup       = 'gatepass';
    protected $table         = 'gatepass_request_approvals';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'request_id', 'node_id', 'role_id', 'user_id', 'slot_priority',
        'status', 'remarks', 'acted_by', 'acted_at', 'created_at',
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    /** The single open step for a request (its current stop in the chain). */
    public function currentStep(int $requestId): ?array
    {
        return $this->where('request_id', $requestId)
                    ->whereIn('status', ['pending', 'floating'])
                    ->orderBy('id', 'DESC')
                    ->first();
    }

    /** Everything sitting in a given approver's personal inbox. */
    public function inboxFor(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->where('status', 'pending')
                    ->findAll();
    }

    /** All steps stuck with nobody resolved to act on them (e.g. TL on leave). */
    public function floating(): array
    {
        return $this->where('status', 'floating')->findAll();
    }
}
