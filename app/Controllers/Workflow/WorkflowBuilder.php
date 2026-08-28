<?php

namespace App\Controllers\Workflow;

use App\Controllers\BaseController;
use App\Models\Workflow\WorkflowAssignmentModel;
use App\Models\Workflow\WorkflowEdgeModel;
use App\Models\Workflow\WorkflowNodeModel;
use App\Models\Workflow\WorkflowTemplateModel;
use Config\Database;

/**
 * Admin-only graph builder for gatepass approval routes. Gate this behind
 * whatever admin filter you already use for the sidebar RBAC (mirrors the
 * prototype's 'admin' filter group).
 *
 * NOTE: role/user pickers for the builder UI need names to display. Wire
 * $this->roleOptions() / $this->userOptions() below to your actual roles
 * and users source (hris_system per your dual-DB setup) - left as stubs
 * since I don't have your RoleModel/UserModel in this chat.
 */
class WorkflowBuilder extends BaseController
{
    public function index()
    {
        $templates = (new WorkflowTemplateModel())->findAll();
        $nodeModel = new WorkflowNodeModel();

        $roles = $this->roleOptions();
        $users = $this->userOptions();
        $roleName = fn ($id) => ($roles[$id]['name'] ?? null);
        $userName = fn ($id) => ($users[$id]['name'] ?? null);

        foreach ($templates as &$t) {
            $nodes = $nodeModel->forTemplate($t['id']);
            $t['node_count'] = count($nodes);
            $start = array_values(array_filter($nodes, fn ($n) => $n['node_type'] === 'start'))[0] ?? null;
            $t['applies_to'] = $start
                ? ($start['user_id'] ? $userName($start['user_id']) . ' only' : ($start['role_id'] ? $roleName($start['role_id']) : '—'))
                : '—';
        }
        unset($t);

        return view('workflow/builder', [
            'templates' => $templates,
            'roles'     => array_values($roles),
            'users'     => array_values($users),
        ]);
    }

    public function show($templateId)
    {
        $nodeModel = new WorkflowNodeModel();
        $edgeModel = new WorkflowEdgeModel();

        return $this->response->setJSON([
            'template'    => (new WorkflowTemplateModel())->find($templateId),
            'nodes'       => $nodeModel->forTemplate($templateId),
            'edges'       => $edgeModel->forTemplate($templateId),
            'assignments' => (new WorkflowAssignmentModel())->where('workflow_template_id', $templateId)->findAll(),
        ]);
    }

    public function destroy($templateId)
    {
        $db = Database::connect('gatepass');
        $db->transStart();
        (new WorkflowAssignmentModel())->where('workflow_template_id', $templateId)->delete();
        (new WorkflowEdgeModel())->where('workflow_template_id', $templateId)->delete();
        (new WorkflowNodeModel())->where('workflow_template_id', $templateId)->delete();
        (new WorkflowTemplateModel())->delete($templateId);
        $db->transComplete();

        return $this->response->setJSON(['ok' => true]);
    }

    /**
     * Validates a graph before it's allowed to save. Identical rules to the
     * prototype: no orphan dots, start/end required, every start/approver
     * node needs an outgoing line, every approver/end node needs an
     * incoming line, every approver dot must be bound to a role or person,
     * no self-loops.
     */
    private function findGraphProblems(array $nodes, array $edges): array
    {
        $problems = [];

        if (empty($nodes)) {
            return ['Add at least a Start dot and an End dot before saving.'];
        }

        $hasStart = false;
        $hasEnd   = false;
        $outgoing = [];
        $incoming = [];

        foreach ($edges as $e) {
            $from = $e['from_tmp_id'] ?? $e['from_node_id'] ?? null;
            $to   = $e['to_tmp_id']   ?? $e['to_node_id']   ?? null;
            if ($from === $to) {
                $problems[] = 'A dot cannot be connected to itself.';
                continue;
            }
            $outgoing[$from] = true;
            $incoming[$to]   = true;
        }

        foreach ($nodes as $n) {
            $id    = $n['tmp_id'] ?? $n['id'];
            $label = $n['label'] ?: ucfirst($n['node_type']);

            if ($n['node_type'] === 'start') {
                $hasStart = true;
                if (empty($outgoing[$id])) {
                    $problems[] = "\"$label\" (start) is not connected to anything - draw a line from it to the first approver.";
                }
                if (empty($n['role_id']) && empty($n['user_id'])) {
                    $problems[] = "\"$label\" (start) isn't bound to a role or a specific person yet - pick who this route applies to.";
                }
            }

            if ($n['node_type'] === 'approver') {
                if (empty($incoming[$id])) {
                    $problems[] = "\"$label\" has no incoming connection - nothing routes into it yet.";
                }
                if (empty($outgoing[$id])) {
                    $problems[] = "\"$label\" has no outgoing connection - the request would have nowhere to go after this dot.";
                }
                if (empty($n['role_id']) && empty($n['user_id'])) {
                    $problems[] = "\"$label\" isn't bound to a role or a specific person yet - pick one.";
                }
            }

            if ($n['node_type'] === 'end') {
                $hasEnd = true;
                if (empty($incoming[$id])) {
                    $problems[] = "\"$label\" (end) is not connected to anything - draw a line into it.";
                }
            }
        }

        if (!$hasStart) {
            $problems[] = 'The route needs exactly one Start dot.';
        }
        if (!$hasEnd) {
            $problems[] = 'The route needs an End dot so approved requests have somewhere to land.';
        }

        return array_values(array_unique($problems));
    }

    public function validateGraph()
    {
        $payload  = $this->request->getJSON(true);
        $problems = $this->findGraphProblems($payload['nodes'] ?? [], $payload['edges'] ?? []);

        return $this->response->setJSON(['ok' => empty($problems), 'problems' => $problems]);
    }

    /**
     * Body shape:
     * {
     *   template: {id?, name, description, request_type},
     *   nodes: [{tmp_id, node_type, role_id, user_id, label, pos_x, pos_y}],
     *   edges: [{from_tmp_id, to_tmp_id}],
     *   assignments: [{applies_to_type, applies_to_id}]
     * }
     * request_type defaults to 'gatepass' if not sent - your builder UI can
     * hide that field entirely if this module only ever handles gatepass
     * requests (vs. the prototype's generic multi-request-type design).
     */
    public function save()
    {
        $payload = $this->request->getJSON(true);

        $problems = $this->findGraphProblems($payload['nodes'] ?? [], $payload['edges'] ?? []);
        if (!empty($problems)) {
            return $this->response->setStatusCode(422)->setJSON(['ok' => false, 'problems' => $problems]);
        }

        $templateModel = new WorkflowTemplateModel();
        $nodeModel     = new WorkflowNodeModel();
        $edgeModel     = new WorkflowEdgeModel();
        $assignModel   = new WorkflowAssignmentModel();

        $db = Database::connect('gatepass');
        $db->transStart();

        $t = $payload['template'];
        $requestType = $t['request_type'] ?? 'gatepass';

        if (!empty($t['id'])) {
            $templateId = (int) $t['id'];
            $templateModel->update($templateId, [
                'name'         => $t['name'],
                'description'  => $t['description'] ?? '',
                'request_type' => $requestType,
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
            $edgeModel->where('workflow_template_id', $templateId)->delete();
            $nodeModel->where('workflow_template_id', $templateId)->delete();
            $assignModel->where('workflow_template_id', $templateId)->delete();
        } else {
            $templateId = $templateModel->insert([
                'name'         => $t['name'],
                'description'  => $t['description'] ?? '',
                'request_type' => $requestType,
                'status'       => 'active',
                'created_by'   => session()->get('user_id'),
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ], true);
        }

        $idMap = [];
        foreach ($payload['nodes'] as $n) {
            $realId = $nodeModel->insert([
                'workflow_template_id' => $templateId,
                'node_type'            => $n['node_type'],
                'role_id'              => $n['role_id'] ?: null,
                'user_id'              => $n['user_id'] ?: null,
                'label'                => $n['label'],
                'pos_x'                => $n['pos_x'],
                'pos_y'                => $n['pos_y'],
            ], true);
            $idMap[$n['tmp_id']] = $realId;
        }

        foreach ($payload['edges'] as $e) {
            $edgeModel->insert([
                'workflow_template_id' => $templateId,
                'from_node_id'         => $idMap[$e['from_tmp_id']],
                'to_node_id'           => $idMap[$e['to_tmp_id']],
            ]);
        }

        foreach ($payload['assignments'] ?? [] as $a) {
            if (empty($a['applies_to_id'])) {
                continue;
            }
            $assignModel->insert([
                'workflow_template_id' => $templateId,
                'applies_to_type'      => $a['applies_to_type'],
                'applies_to_id'        => $a['applies_to_id'],
                'request_type'         => $requestType,
            ]);
        }

        $db->transComplete();

        return $this->response->setJSON(['ok' => true, 'template_id' => $templateId]);
    }

    /**
     * Reads your real `role` table (role_id, role_desc, archived). The
     * builder JS expects each entry keyed by `id` with a `name` field, so
     * this maps role_id -> id and role_desc -> name for it.
     */
    private function roleOptions(): array
    {
        $rows = Database::connect('gatepass')
                         ->table('role')
                         ->select('role_id as id, role_desc as name')
                         ->where('archived', 0)
                         ->get()->getResultArray();
        return array_column($rows, null, 'id');
    }

    /**
     * Your `users` table (db_gatepass) has no display-name column - only
     * `username` (e.g. "2024-0001"). Shown as-is in the builder's person
     * picker. If you want real names in the dropdown, join against wherever
     * those live (hris_system, Snipe-IT via snipeit_id, etc.) and alias the
     * result to `name` here - the builder view only needs id + name.
     */
    private function userOptions(): array
    {
        $rows = Database::connect('gatepass')
                         ->table('users')
                         ->select('id, username as name')
                         ->get()->getResultArray();
        return array_column($rows, null, 'id');
    }
}
