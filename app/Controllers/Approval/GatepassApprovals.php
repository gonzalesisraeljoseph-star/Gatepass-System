<?php

namespace App\Controllers\Approval;

use App\Controllers\BaseController;
use App\Libraries\Approval\GatepassApprovalService;
use App\Libraries\Workflow\GatepassRoleResolver;
use App\Models\Approval\GatepassApprovalModel;
use Config\Database;

/**
 * Approver-facing side of the workflow engine.
 *
 * Uses session('logged_in')['ref_emp'] per your existing session structure
 * (matches Gatepass::store() and the rest of the app - the 'logged_in'
 * array is keyed by ref_emp, not user_id).
 * Override capability is checked LIVE via GatepassRoleResolver rather than
 * trusting a session flag - your session doesn't currently set
 * 'can_override' at all (that column didn't exist until this module's
 * migration added it), and checking live also sidesteps the exact class of
 * stale-session problem you're chasing with the role_ids sidebar bug.
 */
class GatepassApprovals extends BaseController
{
    protected string $requestsTable = 'gatepass_requests';

    /** My personal approvals inbox. */
    public function inbox()
    {
        $userId = (int) (session('logged_in')['ref_emp'] ?? 0);

        $approvalModel = new GatepassApprovalModel();
        $mySteps = $approvalModel->inboxFor($userId);

        $db = Database::connect('gatepass');
        $items = array_map(fn ($s) => [
            'step'    => $s,
            'request' => $db->table($this->requestsTable)->where('id', $s['request_id'])->get()->getRowArray(),
        ], $mySteps);

        return view('approvals/inbox', ['items' => $items]);
    }

    public function act()
    {
        $requestId = (int) $this->request->getPost('request_id');
        $userId    = (int) (session('logged_in')['ref_emp'] ?? 0); // never trust a posted user_id
        $decision  = $this->request->getPost('decision'); // approved | rejected
        $remarks   = $this->request->getPost('remarks') ?? '';

        try {
            (new GatepassApprovalService())->act($requestId, $userId, $decision, $remarks);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->to('/approvals')->with('message', "Request #$requestId $decision.");
    }

    /**
     * Requests stuck with nobody resolved to act (e.g. the only Team Lead
     * is on leave), visible to override-capable roles.
     */
    public function floating()
    {
        $userId      = (int) (session('logged_in')['ref_emp'] ?? 0);
        $canOverride = (new GatepassRoleResolver())->isOverrideCapable($userId);

        $approvalModel = new GatepassApprovalModel();
        $db = Database::connect('gatepass');

        $floatingSteps = $approvalModel->floating();
        $items = array_map(fn ($s) => [
            'step'    => $s,
            'request' => $db->table($this->requestsTable)->where('id', $s['request_id'])->get()->getRowArray(),
        ], $floatingSteps);

        return view('approvals/floating', ['items' => $items, 'canOverride' => $canOverride]);
    }

    public function override()
    {
        $userId = (int) (session('logged_in')['ref_emp'] ?? 0);

        if (!(new GatepassRoleResolver())->isOverrideCapable($userId)) {
            return redirect()->back()->with('error', 'You do not have override rights.');
        }

        $requestId = (int) $this->request->getPost('request_id');
        $decision  = $this->request->getPost('decision');
        $remarks   = $this->request->getPost('remarks') ?? '';

        (new GatepassApprovalService())->override($requestId, $userId, $decision, $remarks);

        return redirect()->to('/approvals/floating')->with('message', "Request #$requestId overridden ($decision).");
    }
}