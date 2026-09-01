<?php

namespace App\Libraries\Workflow;

use App\Models\Approval\GatepassApprovalModel;
use App\Models\Workflow\GatepassRequestLogModel;
use App\Models\Workflow\WorkflowAssignmentModel;
use App\Models\Workflow\WorkflowEdgeModel;
use App\Models\Workflow\WorkflowNodeApproverModel;
use App\Models\Workflow\WorkflowNodeModel;
use Config\Database;

/**
 * GatepassWorkflowEngine — ROUTING ONLY.
 * -----------------------
 * Answers "given a request, where does it go next in the graph?" and
 * nothing else. Does not record approve/reject decisions - that's
 * GatepassApprovalService, which depends on this class (never the reverse).
 *
 * Rules:
 *  1. No workflow wired for this requester+type -> auto-approve immediately.
 *  2. Walk node -> edge -> node. Each approver node resolves to a real
 *     available person: either the node's single role_id/user_id (legacy),
 *     OR, if the node has rows in workflow_node_approvers, an ORDERED
 *     CHAIN of slots that must each eventually be satisfied by someone -
 *     tried in priority order, skipping whoever is unavailable when their
 *     slot comes up ("multi-approve" nodes).
 *  3. Can't resolve anyone at a node/slot -> `workflow_status` = floating,
 *     does NOT auto-advance and does NOT auto-approve. An override-capable
 *     user can still force it closed regardless of chain position.
 */
class GatepassWorkflowEngine
{
    public GatepassApprovalModel $approvals;
    protected GatepassRequestLogModel $logs;
    protected WorkflowAssignmentModel $assignments;
    public WorkflowNodeModel $nodes;
    public WorkflowEdgeModel $edges;
    public WorkflowNodeApproverModel $nodeApprovers;
    protected GatepassRoleResolver $people;
    public $db;

    public string $requestsTable = 'gatepass_requests';

    public function __construct()
    {
        $this->approvals     = new GatepassApprovalModel();
        $this->logs          = new GatepassRequestLogModel();
        $this->assignments   = new WorkflowAssignmentModel();
        $this->nodes         = new WorkflowNodeModel();
        $this->edges         = new WorkflowEdgeModel();
        $this->nodeApprovers = new WorkflowNodeApproverModel();
        $this->people        = new GatepassRoleResolver();
        $this->db            = Database::connect('gatepass');
    }

    /**
     * Route an EXISTING gatepass request to its first workflow stop, or
     * auto-approve it. Call right after inserting the request row:
     *
     *   $requestId = $db->table('gatepass_requests')->insert([...]);
     *   (new GatepassWorkflowEngine())->route($requestId, $requestorId);
     */
    public function route(int $requestId, int $requestorId, string $requestType = 'gatepass'): void
    {
        $roleIds    = $this->people->getRoleIdsForUser($requestorId);
        $templateId = $this->assignments->resolveTemplateId($requestorId, $roleIds, $requestType);

        if ($templateId === null) {
            $this->finalize($requestId, 'approved', null);
            $this->log($requestId, 'auto_approved', null, 'No workflow wired for this requester/request type - approved automatically.');
            return;
        }

        $start = $this->nodes->startNode($templateId);
        if (!$start) {
            $this->finalize($requestId, 'approved', null);
            $this->log($requestId, 'auto_approved', null, 'Template has no start node - approved automatically.');
            return;
        }

        $this->db->table($this->requestsTable)->where('id', $requestId)->update([
            'workflow_template_id' => $templateId,
        ]);

        $firstNodeId = $this->edges->nextNodeId($templateId, $start['id']);
        if ($firstNodeId === null) {
            $this->finalize($requestId, 'approved', null);
            $this->log($requestId, 'auto_approved', null, 'Workflow start node has no outgoing connection - approved automatically.');
            return;
        }

        $this->enterNode($requestId, $templateId, $firstNodeId);
    }

    /**
     * Move the request onto $nodeId: resolve an approver (or end it, or
     * float it). Called by route() for the first step, and by
     * GatepassApprovalService::act() for every subsequent step after an
     * approval.
     */
    public function enterNode(int $requestId, int $templateId, int $nodeId): void
    {
        $node = $this->nodes->find($nodeId);

        if ($node['node_type'] === 'end') {
            $this->finalize($requestId, 'approved', $nodeId);
            $this->log($requestId, 'approved', null, 'Reached end of workflow.');
            return;
        }

        $slots = $this->nodeApprovers->forNode($nodeId);

        if (!empty($slots)) {
            $this->enterApprovalSlot($requestId, $nodeId, $slots, null);
            return;
        }

        // Legacy single-approver node (no rows in workflow_node_approvers).
        // NOTE: user_id/role_id can legitimately be 0 (e.g. superadmin's id
        // is 0 in this schema) - empty() treats 0 as falsy, so use isset()
        // plus explicit null/'' checks instead.
        $resolvedUserId = null;
        if (isset($node['user_id']) && $node['user_id'] !== null && $node['user_id'] !== '') {
            $resolvedUserId = $this->people->isAvailable((int) $node['user_id']) ? (int) $node['user_id'] : null;
        } elseif (isset($node['role_id']) && $node['role_id'] !== null && $node['role_id'] !== '') {
            $candidates = $this->people->availableForRole((int) $node['role_id']);
            if (!empty($candidates)) {
                $resolvedUserId = (int) $candidates[0]['id'];
            }
        }

        $workflowStatus = $resolvedUserId ? 'pending' : 'floating';

        $this->approvals->insert([
            'request_id' => $requestId,
            'node_id'    => $nodeId,
            'role_id'    => $node['role_id'],
            'user_id'    => $resolvedUserId,
            'status'     => $workflowStatus,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->table($this->requestsTable)->where('id', $requestId)->update([
            'status'          => 'Pending',
            'workflow_status' => $workflowStatus,
            'current_node_id' => $nodeId,
        ]);

        if ($workflowStatus === 'floating') {
            $this->log($requestId, 'floating', null, 'No available approver for node "' . $node['label'] . '". Awaiting escalation/override.');
        } else {
            $this->log($requestId, 'routed', null, 'Routed to ' . $node['label'] . '.');
        }
    }

    /**
     * Called by GatepassApprovalService::act() after a multi-approve slot
     * has been approved. Returns true if another slot on this SAME node
     * still needs approving (caller should stop - a new pending/floating
     * step was just created for it). Returns false once every slot's
     * priority has been consumed, meaning the caller should advance to the
     * next node via the edges table as usual.
     */
    public function advanceMultiApproverNode(int $requestId, int $nodeId, int $completedPriority): bool
    {
        $slots = $this->nodeApprovers->forNode($nodeId);
        if (empty($slots)) {
            return false;
        }

        $remaining = array_filter($slots, fn ($s) => (int) $s['priority'] > $completedPriority);
        if (empty($remaining)) {
            return false; // every slot's priority has now been used up
        }

        $this->enterApprovalSlot($requestId, $nodeId, $slots, $completedPriority);
        return true;
    }

    /**
     * Resolve the next required slot after $afterPriority (null = start of
     * the chain) and create its approval step. Tries slots in priority
     * order and hands the step to the first AVAILABLE person; if nobody
     * remaining is available right now, floats on the earliest unresolved
     * slot rather than skipping the node entirely.
     */
    private function enterApprovalSlot(int $requestId, int $nodeId, array $slots, ?int $afterPriority): void
    {
        $node = $this->nodes->find($nodeId);

        $resolvedUserId = null;
        $chosenSlot      = null;

        foreach ($slots as $slot) {
            if ($afterPriority !== null && (int) $slot['priority'] <= $afterPriority) {
                continue;
            }

            $candidate = null;
            if (isset($slot['user_id']) && $slot['user_id'] !== null && $slot['user_id'] !== '') {
                $candidate = $this->people->isAvailable((int) $slot['user_id']) ? (int) $slot['user_id'] : null;
            } elseif (isset($slot['role_id']) && $slot['role_id'] !== null && $slot['role_id'] !== '') {
                $found = $this->people->availableForRole((int) $slot['role_id']);
                if (!empty($found)) {
                    $candidate = (int) $found[0]['id'];
                }
            }

            if ($chosenSlot === null) {
                $chosenSlot = $slot; // remember earliest remaining slot in case nobody's available
            }

            if ($candidate !== null) {
                $resolvedUserId = $candidate;
                $chosenSlot      = $slot;
                break;
            }
        }

        if ($chosenSlot === null) {
            // No slots at all remain past $afterPriority - shouldn't happen
            // since advanceMultiApproverNode() checks this first, but guard
            // anyway rather than silently doing nothing.
            return;
        }

        $workflowStatus = $resolvedUserId ? 'pending' : 'floating';

        $this->approvals->insert([
            'request_id'    => $requestId,
            'node_id'       => $nodeId,
            'role_id'       => $chosenSlot['role_id'],
            'user_id'       => $resolvedUserId,
            'slot_priority' => $chosenSlot['priority'],
            'status'        => $workflowStatus,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        $this->db->table($this->requestsTable)->where('id', $requestId)->update([
            'status'          => 'Pending',
            'workflow_status' => $workflowStatus,
            'current_node_id' => $nodeId,
        ]);

        if ($workflowStatus === 'floating') {
            $this->log($requestId, 'floating', null, 'No available approver for node "' . $node['label'] . '" (slot ' . $chosenSlot['priority'] . '). Awaiting escalation/override.');
        } else {
            $this->log($requestId, 'routed', null, 'Routed to ' . $node['label'] . ' (slot ' . $chosenSlot['priority'] . ').');
        }
    }

    /**
     * Writes both status columns at a terminal state. Public because
     * GatepassApprovalService calls this directly for rejected/overridden
     * outcomes, and for the "approved, no further node" case.
     */
    public function finalize(int $requestId, string $workflowStatus, ?int $nodeId): void
    {
        $legacyStatus = match ($workflowStatus) {
            'approved', 'overridden' => 'Approved',
            'rejected'                => 'Rejected',
            default                   => 'Pending',
        };

        $data = [
            'status'          => $legacyStatus,
            'workflow_status' => $workflowStatus,
        ];
        if ($nodeId !== null) {
            $data['current_node_id'] = $nodeId;
        }

        $this->db->table($this->requestsTable)->where('id', $requestId)->update($data);
    }

    /** Public so GatepassApprovalService writes to the same audit trail. */
    public function log(int $requestId, string $action, ?int $actorId, string $notes): void
    {
        $this->logs->insert([
            'request_id' => $requestId,
            'action'     => $action,
            'actor_id'   => $actorId,
            'notes'      => $notes,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
