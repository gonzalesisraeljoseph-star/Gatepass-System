<?php

namespace App\Libraries\Workflow;

use App\Models\Approval\GatepassApprovalModel;
use App\Models\Workflow\GatepassRequestLogModel;
use App\Models\Workflow\WorkflowAssignmentModel;
use App\Models\Workflow\WorkflowEdgeModel;
use App\Models\Workflow\WorkflowNodeModel;
use Config\Database;

/**
 * GatepassWorkflowEngine — ROUTING ONLY.
 * -----------------------
 * Answers "given a request, where does it go next in the graph?" and
 * nothing else. Does not record approve/reject decisions - that's
 * GatepassApprovalService, which depends on this class (never the reverse).
 * Split deliberately so you can branch/version the two independently:
 * this is where future decision-based branching (approve -> node A,
 * reject -> node B, instead of reject always ending the request) will live,
 * without touching how a decision gets recorded.
 *
 * Rules:
 *  1. No workflow wired for this requester+type -> auto-approve immediately.
 *  2. Walk node -> edge -> node. Each approver node resolves to a real
 *     available person (specific user, or any available holder of a role).
 *  3. Can't resolve anyone at a node -> `workflow_status` = floating, does
 *     NOT auto-advance and does NOT auto-approve.
 *
 * `finalize()` and `log()` are public/protected here (not private) because
 * GatepassApprovalService calls them directly when a decision closes out a
 * request (rejected, or approved with no further node) or forces an
 * override - keeps "how do we write a terminal state to the request row"
 * in one place regardless of which class triggered it.
 */
class GatepassWorkflowEngine
{
    public GatepassApprovalModel $approvals;
    protected GatepassRequestLogModel $logs;
    protected WorkflowAssignmentModel $assignments;
    public WorkflowNodeModel $nodes;
    public WorkflowEdgeModel $edges;
    protected GatepassRoleResolver $people;
    public $db;

    public string $requestsTable = 'gatepass_requests';

    public function __construct()
    {
        $this->approvals   = new GatepassApprovalModel();
        $this->logs        = new GatepassRequestLogModel();
        $this->assignments = new WorkflowAssignmentModel();
        $this->nodes       = new WorkflowNodeModel();
        $this->edges       = new WorkflowEdgeModel();
        $this->people      = new GatepassRoleResolver();
        $this->db          = Database::connect('gatepass');
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
     *
     * NOTE for future branching: right now this only ever receives the
     * single "next" node from a linear edge lookup. When you add
     * decision-based branching, this is the method whose caller needs to
     * pick between multiple outgoing edges (e.g. by decision type) before
     * calling in - enterNode() itself doesn't need to change, it just
     * enters whatever node id it's given.
     */
    public function enterNode(int $requestId, int $templateId, int $nodeId): void
    {
        $node = $this->nodes->find($nodeId);

        if ($node['node_type'] === 'end') {
            $this->finalize($requestId, 'approved', $nodeId);
            $this->log($requestId, 'approved', null, 'Reached end of workflow.');
            return;
        }

        $resolvedUserId = null;
        if (!empty($node['user_id'])) {
            $resolvedUserId = $this->people->isAvailable((int) $node['user_id']) ? (int) $node['user_id'] : null;
        } elseif (!empty($node['role_id'])) {
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
            'status'          => 'Pending', // legacy enum has no floating/routed concept
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
