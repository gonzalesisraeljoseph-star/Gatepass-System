<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;
use App\Libraries\Workflow\GatepassWorkflowEngine;
use App\Libraries\AccountabilityPdf;
use App\Models\userRoleModel;

class Gatepass extends BaseController
{
    private const ACCOUNTABILITY_UPLOAD_DIR = WRITEPATH . 'uploads/accountability/';
    private const ACCOUNTABILITY_ALLOWED_EXTENSIONS = ['pdf', 'png', 'jpg', 'jpeg', 'docx', 'xlsx'];
    private const ACCOUNTABILITY_MAX_FILE_SIZE = 10485760;

    public function __construct()
    {
        if (!session()->get('logged_in')) {
            header('Location: ' . base_url('/'));
            exit;
        }
    }
    public function index()
    {
        $sessionUser = session()->get('logged_in');
        $refEmp = (int) ($sessionUser['ref_emp'] ?? 0);

        return view('pages/request', [
            'canCreateRequest' => $refEmp > 0,
            'canManageAccountability' => (new userRoleModel())->isAdministrator($refEmp),
            'accountabilityTemplates' => $this->accountabilityTemplateOptions(),
        ]);
    }

    public function store()
{
    $db = \Config\Database::connect('gatepass');

    $requestorId    = (int) (session()->get('logged_in')['ref_emp'] ?? 0);
    if ($requestorId <= 0) {
        return $this->response->setStatusCode(422)->setJSON([
            'status'  => false,
            'message' => 'Your account is not linked to an employee profile, so it cannot submit a request.',
        ]);
    }
    $reason         = $this->request->getPost('reason');
    $devices        = $this->request->getPost('devices');
    $requestor_name = $this->request->getPost('requestor_name');

    $db->table('gatepass_requests')->insert([
        'requestor_id'   => $requestorId,
        'reason'         => $reason,
        'requestor_name' => $requestor_name,
        'request_type'   => 'gatepass',
        'status'         => 'Pending'
    ]);

    $gatepassId = $db->insertID();

    if (!empty($devices)) {
        foreach ($devices as $deviceId) {
            $db->table('gatepass_request_items')->insert([
                'gatepass_id' => $gatepassId,
                'device_id'   => $deviceId
            ]);
        }
    }

    // Route the newly created request onto its workflow: resolves the
    // assigned template, finds the first approver (or auto-approves if
    // no workflow is wired), and inserts the pending approval row.
    (new GatepassWorkflowEngine())->route($gatepassId, (int) $requestorId, 'gatepass');

    return $this->response->setJSON([
        'status'  => true,
        'message' => 'Gatepass request submitted successfully'
    ]);
}

    public function storeAccountability()
    {
        $requestorId = (int) (session()->get('logged_in')['ref_emp'] ?? 0);
        if ($requestorId <= 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'message' => 'Your account is not linked to an employee profile.',
            ]);
        }

        $items = $this->request->getPost('items');
        if (!is_array($items) || count($items) < 1) {
            return $this->response->setStatusCode(422)->setJSON(['status' => false, 'message' => 'Add at least one equipment item.']);
        }

        $fields = ['department', 'mobile', 'address', 'email'];
        $profile = [];
        foreach ($fields as $field) {
            $profile[$field] = trim((string) $this->request->getPost($field));
        }
        $name = trim((string) $this->request->getPost('requestor_name'));
        if ($name === '') {
            return $this->response->setStatusCode(422)->setJSON(['status' => false, 'message' => 'Employee name is required.']);
        }
        foreach ($items as $item) {
            if (trim((string) ($item['description'] ?? '')) === '' || trim((string) ($item['employee'] ?? '')) === '') {
                return $this->response->setStatusCode(422)->setJSON(['status' => false, 'message' => 'Each equipment row needs a description and employee.']);
            }
        }
        $templateKey = $this->assignedAccountabilityTemplateKey();
        $templateKeys = array_column($this->accountabilityTemplateOptions(), 'template_key');
        if (!in_array($templateKey, $templateKeys, true)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => false, 'message' => 'Choose an available accountability document format.']);
        }
        $db = \Config\Database::connect('gatepass');
        $db->transBegin();
        $year = (int) date('Y');
        $db->query('INSERT IGNORE INTO gatepass_accountability_sequences (sequence_year, last_number) VALUES (?, 0)', [$year]);
        $sequence = $db->query('SELECT last_number FROM gatepass_accountability_sequences WHERE sequence_year = ? FOR UPDATE', [$year])->getRowArray();
        $nextNumber = (int) ($sequence['last_number'] ?? 0) + 1;
        $db->table('gatepass_accountability_sequences')->where('sequence_year', $year)->update(['last_number' => $nextNumber]);
        $documentNumber = sprintf('ITAF%04d%d', $nextNumber, $year);
        $db->table('gatepass_requests')->insert([
            'requestor_id' => $requestorId,
            'requestor_name' => $name,
            'reason' => 'IT Accountability Form',
            'request_type' => 'accountability',
            'accountability_template_key' => $templateKey,
            'document_number' => $documentNumber,
            'department' => $profile['department'],
            'mobile' => $profile['mobile'],
            'address' => $profile['address'],
            'email' => $profile['email'],
            'form_date' => $this->request->getPost('form_date') ?: date('Y-m-d'),
            'signature_name' => trim((string) $this->request->getPost('signature_name')),
            'verified_by' => trim((string) $this->request->getPost('verified_by')),
            'approved_by' => trim((string) $this->request->getPost('approved_by')),
            'acknowledgment_accepted' => 1,
            'status' => 'Pending',
        ]);
        $requestId = (int) $db->insertID();
        foreach ($items as $item) {
            $db->table('gatepass_accountability_items')->insert([
                'request_id' => $requestId,
                'description' => trim((string) ($item['description'] ?? '')),
                'model_brand' => trim((string) ($item['model_brand'] ?? '')),
                'serial_number' => trim((string) ($item['serial_number'] ?? '')),
                'employee' => trim((string) ($item['employee'] ?? '')),
                'remarks' => trim((string) ($item['remarks'] ?? '')),
                'item_date' => ($item['item_date'] ?? '') !== '' ? $item['item_date'] : null,
            ]);
        }
        if (!$db->transStatus()) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Unable to save the accountability request.']);
        }
        $db->transCommit();

        (new GatepassWorkflowEngine())->route($requestId, $requestorId, 'accountability');
        return $this->response->setJSON(['status' => true, 'message' => 'IT Accountability Form submitted for approval.']);
    }

    public function listAccountability()
    {
        $session = session()->get('logged_in') ?? [];
        $refEmp = (int) ($session['ref_emp'] ?? 0);
        $isAdmin = (new userRoleModel())->isAdministrator($refEmp);
        if ($refEmp <= 0 && !$isAdmin) {
            return $this->response->setJSON(['data' => []]);
        }
        $query = \Config\Database::connect('gatepass')->table('gatepass_requests')
            ->where('request_type', 'accountability')->orderBy('id', 'DESC');
        if (!$isAdmin) $query->where('requestor_id', $refEmp);
        $rows = $query->get()->getResultArray();
        $db = \Config\Database::connect('gatepass');
        return $this->response->setJSON(['data' => $rows]);
    }

    public function downloadAccountabilityPdf(int $requestId)
    {
        if (!$this->canAccessAccountabilityRequest($requestId)) return $this->response->setStatusCode(404)->setBody('Document not found.');
        $db = \Config\Database::connect('gatepass');
        $request = $db->table('gatepass_requests')->where(['id' => $requestId, 'request_type' => 'accountability'])->get()->getRowArray();
        if (!$request) return $this->response->setStatusCode(404)->setBody('Document not found.');
        try {
            $requestUser = \Config\Database::connect('hris')->table('tbl_user_info u')
                ->select('u.username, e.full_name')
                ->join('v_profile_employee e', 'e.profile_id = u.ref_emp', 'left')
                ->where('u.ref_emp', (int) ($request['requestor_id'] ?? 0))
                ->get()->getRowArray();
            $request['user'] = trim((string) ($requestUser['full_name'] ?? '')) ?: (string) ($requestUser['username'] ?? $request['requestor_name'] ?? '');
        } catch (\Throwable $e) {
            log_message('warning', 'Unable to resolve request user for accountability document ' . $requestId . ': ' . $e->getMessage());
            $request['user'] = (string) ($request['requestor_name'] ?? '');
        }
        $items = $db->table('gatepass_accountability_items')->where('request_id', $requestId)->orderBy('id')->get()->getResultArray();
        $pdf = (new AccountabilityPdf())->generate($request, $items, $this->accountabilityDocumentSettings((string) ($request['accountability_template_key'] ?? 'itaf')));
        $fileName = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $request['document_number']) . '.pdf';
        return $this->response->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Content-Length', (string) strlen($pdf))
            ->setBody($pdf);
    }

    public function adminShowAccountability(int $requestId)
    {
        if (!$this->isCurrentUserAdministrator()) return $this->response->setStatusCode(403)->setJSON(['status' => false, 'message' => 'Administrator access required.']);
        $db = \Config\Database::connect('gatepass');
        $request = $db->table('gatepass_requests')->where(['id' => $requestId, 'request_type' => 'accountability'])->get()->getRowArray();
        if (!$request) return $this->response->setStatusCode(404)->setJSON(['status' => false, 'message' => 'Accountability document not found.']);
        $request['items'] = $db->table('gatepass_accountability_items')->where('request_id', $requestId)->orderBy('id')->get()->getResultArray();
        return $this->response->setJSON(['status' => true, 'request' => $request]);
    }

    public function adminUpdateAccountability(int $requestId)
    {
        if (!$this->isCurrentUserAdministrator()) return $this->response->setStatusCode(403)->setJSON(['status' => false, 'message' => 'Administrator access required.']);
        $db = \Config\Database::connect('gatepass');
        $request = $db->table('gatepass_requests')->where(['id' => $requestId, 'request_type' => 'accountability'])->get()->getRowArray();
        if (!$request) return $this->response->setStatusCode(404)->setJSON(['status' => false, 'message' => 'Accountability document not found.']);
        $name = trim((string) $this->request->getPost('requestor_name'));
        $department = trim((string) $this->request->getPost('department'));
        $date = trim((string) $this->request->getPost('form_date'));
        $issued = \DateTime::createFromFormat('Y-m-d', $date);
        $items = $this->request->getPost('items');
        if ($name === '' || $department === '' || !$issued || $issued->format('Y-m-d') !== $date || !is_array($items) || !$items) {
            return $this->response->setStatusCode(422)->setJSON(['status' => false, 'message' => 'Enter employee details, issue date, and at least one equipment item.']);
        }
        foreach ($items as $item) {
            if (trim((string) ($item['description'] ?? '')) === '' || trim((string) ($item['employee'] ?? '')) === '') {
                return $this->response->setStatusCode(422)->setJSON(['status' => false, 'message' => 'Each equipment row needs a description and employee.']);
            }
        }
        $db->transBegin();
        $db->table('gatepass_requests')->where('id', $requestId)->update([
            'requestor_name' => $name, 'department' => $department, 'form_date' => $date,
            'mobile' => trim((string) $this->request->getPost('mobile')),
            'address' => trim((string) $this->request->getPost('address')),
            'email' => trim((string) $this->request->getPost('email')),
            'signature_name' => trim((string) $this->request->getPost('signature_name')),
            'verified_by' => trim((string) $this->request->getPost('verified_by')),
            'approved_by' => trim((string) $this->request->getPost('approved_by')),
        ]);
        $db->table('gatepass_accountability_items')->where('request_id', $requestId)->delete();
        foreach ($items as $item) {
            $db->table('gatepass_accountability_items')->insert([
                'request_id' => $requestId,
                'description' => trim((string) ($item['description'] ?? '')),
                'model_brand' => trim((string) ($item['model_brand'] ?? '')),
                'serial_number' => trim((string) ($item['serial_number'] ?? '')),
                'employee' => trim((string) ($item['employee'] ?? '')),
                'remarks' => trim((string) ($item['remarks'] ?? '')),
                'item_date' => ($item['item_date'] ?? '') !== '' ? $item['item_date'] : null,
            ]);
        }
        if (!$db->transStatus()) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Unable to update the document.']);
        }
        $db->transCommit();
        return $this->response->setJSON(['status' => true, 'message' => 'Accountability document updated.']);
    }

    private function isCurrentUserAdministrator(): bool
    {
        $userId = (int) (session()->get('logged_in')['ref_emp'] ?? 0);
        return $userId > 0 && (new userRoleModel())->isAdministrator($userId);
    }

    public function viewAccountabilityAttachment(int $attachmentId)
    {
        return $this->serveAccountabilityAttachment($attachmentId, false);
    }

    public function downloadAccountabilityAttachment(int $attachmentId)
    {
        return $this->serveAccountabilityAttachment($attachmentId, true);
    }

    public function replaceAccountabilityAttachment(int $attachmentId)
    {
        $record = $this->accountabilityAttachment($attachmentId);
        if (!$record || !$this->canManageAccountabilityRequest((int) $record['request_id'])) {
            return $this->response->setStatusCode(404)->setJSON(['status' => false, 'message' => 'Attachment not found.']);
        }
        $file = $this->request->getFile('attachment');
        if (!$file || !$file->isValid() || $file->getSize() > self::ACCOUNTABILITY_MAX_FILE_SIZE || !in_array(strtolower($file->getExtension()), self::ACCOUNTABILITY_ALLOWED_EXTENSIONS, true)) {
            return $this->response->setStatusCode(422)->setJSON(['status' => false, 'message' => 'Choose a PDF, PNG, JPG, DOCX, or XLSX file up to 10 MB.']);
        }
        $newRecord = $this->storeAccountabilityAttachment($file, (int) $record['request_id']);
        if (!$newRecord) return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Unable to replace the attachment.']);
        $db = \Config\Database::connect('gatepass');
        $db->table('gatepass_accountability_attachments')->where('id', $attachmentId)->delete();
        $oldPath = self::ACCOUNTABILITY_UPLOAD_DIR . basename($record['stored_name']);
        if (is_file($oldPath)) @unlink($oldPath);
        return $this->response->setJSON(['status' => true, 'message' => 'Attachment replaced.']);
    }

    public function addAccountabilityAttachment(int $requestId)
    {
        if (!$this->canManageAccountabilityRequest($requestId)) {
            return $this->response->setStatusCode(404)->setJSON(['status' => false, 'message' => 'Request not found.']);
        }
        $files = array_values(array_filter($this->request->getFileMultiple('attachments') ?? [], static fn ($f) => $f && $f->getError() !== UPLOAD_ERR_NO_FILE));
        if (!$files || count($files) > 5) return $this->response->setStatusCode(422)->setJSON(['status' => false, 'message' => 'Choose between one and five attachments.']);
        $db = \Config\Database::connect('gatepass');
        $existingCount = $db->table('gatepass_accountability_attachments')->where('request_id', $requestId)->countAllResults();
        if ($existingCount + count($files) > 5) return $this->response->setStatusCode(422)->setJSON(['status' => false, 'message' => 'A request can have up to five attachments.']);
        foreach ($files as $file) {
            if (!$file->isValid() || $file->getSize() > self::ACCOUNTABILITY_MAX_FILE_SIZE || !in_array(strtolower($file->getExtension()), self::ACCOUNTABILITY_ALLOWED_EXTENSIONS, true)) {
                return $this->response->setStatusCode(422)->setJSON(['status' => false, 'message' => 'Attachments must be PDF, PNG, JPG, DOCX, or XLSX files up to 10 MB each.']);
            }
        }
        foreach ($files as $file) {
            if (!$this->storeAccountabilityAttachment($file, $requestId)) return $this->response->setStatusCode(500)->setJSON(['status' => false, 'message' => 'Unable to save an attachment.']);
        }
        return $this->response->setJSON(['status' => true, 'message' => 'Attachment uploaded.']);
    }

    public function deleteAccountabilityAttachment(int $attachmentId)
    {
        $record = $this->accountabilityAttachment($attachmentId);
        if (!$record || !$this->canManageAccountabilityRequest((int) $record['request_id'])) {
            return $this->response->setStatusCode(404)->setJSON(['status' => false, 'message' => 'Attachment not found.']);
        }
        $db = \Config\Database::connect('gatepass');
        $db->table('gatepass_accountability_attachments')->where('id', $attachmentId)->delete();
        $path = self::ACCOUNTABILITY_UPLOAD_DIR . basename($record['stored_name']);
        if (is_file($path)) @unlink($path);
        return $this->response->setJSON(['status' => true, 'message' => 'Attachment deleted.']);
    }

    private function serveAccountabilityAttachment(int $attachmentId, bool $download)
    {
        $record = $this->accountabilityAttachment($attachmentId);
        if (!$record || !$this->canAccessAccountabilityRequest((int) $record['request_id'])) {
            return $this->response->setStatusCode(404)->setBody('Attachment not found.');
        }
        $path = self::ACCOUNTABILITY_UPLOAD_DIR . basename($record['stored_name']);
        if (!is_file($path)) return $this->response->setStatusCode(404)->setBody('Attachment file not found.');
        $mime = in_array($record['mime_type'], ['application/pdf', 'image/png', 'image/jpeg'], true) ? $record['mime_type'] : 'application/octet-stream';
        $disposition = $download ? 'attachment' : 'inline';
        $filename = str_replace(["\r", "\n", '"'], '', $record['original_name']);
        return $this->response->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', $disposition . '; filename="' . $filename . '"')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody(file_get_contents($path));
    }

    private function accountabilityAttachment(int $id): ?array
    {
        return \Config\Database::connect('gatepass')->table('gatepass_accountability_attachments')
            ->where('id', $id)->get()->getRowArray() ?: null;
    }

    private function accountabilityTemplateOptions(): array
    {
        try {
            $db = \Config\Database::connect('gatepass');
            $assigned = $this->assignedAccountabilityTemplateKey();
            $template = $db->table('gatepass_document_templates')->select('template_key, template_name')->where('template_key', $assigned)->get()->getRowArray();
            if ($template) return [$template];
            return $db->table('gatepass_document_templates')->select('template_key, template_name')->orderBy('template_key')->limit(1)->get()->getResultArray();
        }
        catch (\Throwable $e) { return [['template_key' => 'itaf', 'template_name' => 'IT Accountability Form']]; }
    }

    private function assignedAccountabilityTemplateKey(): string
    {
        try {
            $row = \Config\Database::connect('gatepass')->table('gatepass_document_assignments')
                ->select('template_key')->where('form_type', 'itaf')->get()->getRowArray();
            if (!empty($row['template_key'])) return (string) $row['template_key'];
        } catch (\Throwable $e) {
            log_message('warning', 'Accountability document assignment is not available yet: ' . $e->getMessage());
        }
        return 'itaf';
    }

    private function accountabilityDocumentSettings(string $templateKey): array
    {
        try {
            $row = \Config\Database::connect('gatepass')->table('gatepass_document_templates')->where('template_key', $templateKey)->get()->getRowArray();
            if ($row) return $row;
        } catch (\Throwable $e) {
            log_message('warning', 'Accountability document settings are not available yet: ' . $e->getMessage());
        }
        return ['header_text' => 'KINECT INC.', 'document_title' => 'IT ACCOUNTABILITY FORM', 'equipment_heading' => 'IT Equipment Units', 'acknowledgment' => 'This acknowledges that I am accountable for the above items documented in IT AF (IT Accountability Form).', 'footer_text' => 'www.kinectinc.com'];
    }

    private function canManageAccountabilityRequest(int $requestId): bool
    {
        $userId = (int) (session()->get('logged_in')['ref_emp'] ?? 0);
        if ($userId <= 0) return false;
        $db = \Config\Database::connect('gatepass');
        $request = $db->table('gatepass_requests')->where(['id' => $requestId, 'request_type' => 'accountability'])->get()->getRowArray();
        return $request && ((int) $request['requestor_id'] === $userId || (new userRoleModel())->isAdministrator($userId));
    }

    private function canAccessAccountabilityRequest(int $requestId): bool
    {
        if ($this->canManageAccountabilityRequest($requestId)) return true;
        $userId = (int) (session()->get('logged_in')['ref_emp'] ?? 0);
        if ($userId <= 0) return false;
        $db = \Config\Database::connect('gatepass');
        return $db->table('gatepass_request_approvals')->where(['request_id' => $requestId, 'user_id' => $userId])->countAllResults() > 0;
    }

    private function storeAccountabilityAttachment($file, int $requestId): ?array
    {
        if (!is_dir(self::ACCOUNTABILITY_UPLOAD_DIR) && !mkdir(self::ACCOUNTABILITY_UPLOAD_DIR, 0770, true) && !is_dir(self::ACCOUNTABILITY_UPLOAD_DIR)) return null;
        $extension = strtolower($file->getExtension());
        $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
        if (!$file->move(self::ACCOUNTABILITY_UPLOAD_DIR, $storedName)) return null;
        $record = [
            'request_id' => $requestId,
            'original_name' => substr(basename($file->getClientName()), 0, 255),
            'stored_name' => $storedName,
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'file_size' => (int) $file->getSize(),
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $db = \Config\Database::connect('gatepass');
        if (!$db->table('gatepass_accountability_attachments')->insert($record)) {
            @unlink(self::ACCOUNTABILITY_UPLOAD_DIR . $storedName);
            return null;
        }
        $record['id'] = (int) $db->insertID();
        return $record;
    }

    public function list()
{
    $sessionData = session()->get('logged_in') ?? [];
    $refEmp = (int) ($sessionData['ref_emp'] ?? 0);
    $isAdmin = (new userRoleModel())->isAdministrator($refEmp);

    if ($refEmp <= 0 && !$isAdmin) {
        return $this->response->setJSON(['data' => []]);
    }

    $db = \Config\Database::connect('gatepass');

    $query = $db->table('gatepass_requests g')
        ->select('g.*')
        ->where('g.request_type', 'gatepass')
        ->orderBy('g.id', 'DESC');

    if (!$isAdmin) {
        $query->where('g.requestor_id', $refEmp);
    }

    $data = $query->get()->getResultArray();

    return $this->response->setJSON([
        "data" => $data
    ]);
}
}
