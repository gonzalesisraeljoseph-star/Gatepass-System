<?php

namespace App\Libraries\Approval;

use App\Libraries\Workflow\GatepassWorkflowEngine;

/**
 * GatepassApprovalService — APPROVAL DECISIONS ONLY.
 * -----------------------
 * Answers "someone acted on a step - what decision was recorded, and does
 * that close the request or hand it back to the router for the next node?"
 * Depends on GatepassWorkflowEngine (routing) to actually move the request
 * forward; never the other way around.
 */
class GatepassApprovalService
{
    protected GatepassWorkflowEngine $workflow;

    public function __construct(?GatepassWorkflowEngine $workflow = null)
    {
        $this->workflow = $workflow ?? new GatepassWorkflowEngine();
    }

    /** An approver acts on the request's current step. */
    public function act(int $requestId, int $actingUserId, string $decision, string $remarks = ''): void
    {
        $request = $this->workflow->db->table($this->workflow->requestsTable)
                                       ->where('id', $requestId)
                                       ->get()->getRowArray();
        $step = $this->workflow->approvals->currentStep($requestId);

        if (!$step) {
            throw new \RuntimeException('This request has no open approval step to act on.');
        }
        if ((int) $step['user_id'] !== $actingUserId) {
            throw new \RuntimeException('You are not the resolved approver for this step.');
        }

        $this->workflow->approvals->update($step['id'], [
            'status'   => $decision, // approved | rejected
            'remarks'  => $remarks,
            'acted_by' => $actingUserId,
            'acted_at' => date('Y-m-d H:i:s'),
        ]);

        if ($decision === 'rejected') {
            $this->workflow->finalize($requestId, 'rejected', (int) $step['node_id']);
            $this->workflow->log($requestId, 'rejected', $actingUserId, $remarks);
            return;
        }

        $this->workflow->log($requestId, 'approved_step', $actingUserId, $remarks);

        // Multi-approve node: if this step belonged to an ordered slot
        // chain and slots remain, move to the next slot on the SAME node
        // instead of advancing to the next node in the graph.
        if (array_key_exists('slot_priority', $step) && $step['slot_priority'] !== null) {
            $stillOnThisNode = $this->workflow->advanceMultiApproverNode(
                $requestId,
                (int) $step['node_id'],
                (int) $step['slot_priority']
            );
            if ($stillOnThisNode) {
                return; // another slot is now pending/floating - stop here
            }
            // every slot satisfied - fall through to the normal edge advance below
        }

        // Straight-line lookup for now (one outgoing edge). This is the
        // spot to change once you add decision-based branching - pick the
        // edge whose condition matches $decision instead of always taking
        // the single next node.
        $nextNodeId = $this->workflow->edges->nextNodeId((int) $request['workflow_template_id'], (int) $step['node_id']);

        if ($nextNodeId === null) {
            $this->workflow->finalize($requestId, 'approved', (int) $step['node_id']);
            $this->workflow->log($requestId, 'approved', null, 'No further connection from this node - final approval.');
            return;
        }

        $this->workflow->enterNode($requestId, (int) $request['workflow_template_id'], $nextNodeId);
    }

    /**
     * Force a pending/floating request to a final state. Caller (controller)
     * must have already checked GatepassRoleResolver::isOverrideCapable().
     * Works regardless of whether the current step belongs to a legacy
     * single-approver node or a multi-approve slot chain - it just closes
     * out whatever the current open step is.
     */
    public function override(int $requestId, int $overriderId, string $decision, string $remarks = ''): void
    {
        $step = $this->workflow->approvals->currentStep($requestId);
        if ($step) {
            $this->workflow->approvals->update($step['id'], [
                'status'   => 'overridden',
                'remarks'  => $remarks,
                'acted_by' => $overriderId,
                'acted_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // legacy `status` has no "overridden" value - approved override
        // maps to legacy 'Approved', rejected override maps to 'Rejected'.
        $this->workflow->finalize(
            $requestId,
            $decision === 'approved' ? 'overridden' : 'rejected',
            $step['node_id'] ?? null
        );
        $this->workflow->log(
            $requestId,
            'overridden',
            $overriderId,
            ($decision === 'approved' ? 'Force-approved. ' : 'Force-rejected. ') . $remarks
        );
    }
}
