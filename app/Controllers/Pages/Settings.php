<?php

namespace App\Controllers\Pages;

use App\Controllers\BaseController;
use App\Models\userManagementModel;
use App\Models\userRoleModel;

class Settings extends BaseController
{
    public function index()
    {
        $sessionUser = session()->get('logged_in');
        if (!$sessionUser) {
            return redirect()->to('/');
        }

        $model = new userManagementModel();
        $account = !empty($sessionUser['user_id'])
            ? $model->find($sessionUser['user_id'])
            : $model->findByUsername((string) ($sessionUser['username'] ?? ''));

        $canManageDocument = (new userRoleModel())->isAdministrator((int) ($sessionUser['ref_emp'] ?? -1));

        return view('pages/settings', [
            'account' => $account ?? [],
            'sessionUser' => $sessionUser,
            'canManageDocument' => $canManageDocument,
            'documentTemplates' => $this->loadAccountabilityDocumentTemplates(),
            'documentAssignments' => $this->loadDocumentAssignments(),
            'assignedAccountabilityTemplateKey' => $this->assignedAccountabilityTemplateKey(),
        ]);
    }

    public function saveAccountabilityDocument()
    {
        if (!$this->canManageDocument()) return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'Only an administrator can edit the accountability document.']);
        $formType = trim((string) $this->request->getPost('form_type'));
        $allowedFormTypes = ['itaf', 'byod', 'egp', 'rrf'];
        if ($formType === '' && (int) $this->request->getServer('CONTENT_LENGTH') > 0 && empty($_POST)) {
            return $this->response->setStatusCode(413)->setJSON(['success' => false, 'message' => 'The document content is larger than the server accepts. Increase PHP post_max_size and restart Apache, then save again.']);
        }
        if (!in_array($formType, $allowedFormTypes, true)) return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Choose a supported document type.']);
        try {
            $db = \Config\Database::connect('gatepass');
            $assignment = $db->table('gatepass_document_assignments')->where('form_type', $formType)->get()->getRowArray();
            $key = (string) ($assignment['template_key'] ?? '');
            if ($key === '') return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'No document format is assigned to this form yet. Assign a template in the document settings first.']);
            $template = $db->table('gatepass_document_templates')->where('template_key', $key)->countAllResults();
            if (!$template) return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'The assigned document template is missing. Run the pending database migrations, then reload settings.']);
            if (!$db->fieldExists('footer_height', 'gatepass_document_templates')) {
                throw new \RuntimeException('The footer_height database migration has not been applied.');
            }
            $settings = [
                'header_text' => $this->cleanEditorHtml((string) $this->request->getPost('header_text')),
                'footer_text' => $this->cleanEditorHtml((string) $this->request->getPost('footer_text')),
                'body_html' => $this->cleanEditorHtml((string) $this->request->getPost('body_html')),
                'margin_left' => $this->cleanMargin('margin_left', 0.75, 0.25, 2.00),
                'margin_right' => $this->cleanMargin('margin_right', 0.75, 0.25, 2.00),
                'margin_top' => $this->cleanMargin('margin_top', 1.50, 0.50, 2.50),
                'margin_bottom' => $this->cleanMargin('margin_bottom', 1.00, 0.25, 2.00),
                'footer_height' => $this->cleanFooterHeight(),
            ];
            if (!$db->table('gatepass_document_templates')->where('template_key', $key)->update($settings + ['updated_at' => date('Y-m-d H:i:s')])) {
                throw new \RuntimeException('The database rejected the document update.');
            }
            $saved = $db->table('gatepass_document_templates')->where('template_key', $key)->get()->getRowArray();
            foreach (['header_text', 'footer_text', 'body_html', 'footer_height'] as $field) {
                $matches = $field === 'footer_height'
                    ? isset($saved[$field]) && (int) $saved[$field] === $settings[$field]
                    : isset($saved[$field]) && $saved[$field] === $settings[$field];
                if (!$matches) {
                    throw new \RuntimeException('The saved ' . $field . ' content did not match the editor.');
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Unable to save assigned document: ' . $e->getMessage());
            $message = str_contains($e->getMessage(), 'footer_height')
                ? 'The database is missing the footer height field. Apply the pending document template migration, then reload Document Settings and save again.'
                : 'The document could not be saved. Reload Document Settings and try again. If it still fails, the server error has been logged for investigation.';
            return $this->response->setStatusCode(503)->setJSON(['success' => false, 'message' => $message]);
        }
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Assigned document saved.',
            'template_key' => $key,
            'assigned_template_key' => $this->assignedAccountabilityTemplateKey(),
            'assignments' => $this->loadDocumentAssignments(),
        ]);
    }

    private function canManageDocument(): bool
    {
        $user = session()->get('logged_in');
        return !empty($user) && (new userRoleModel())->isAdministrator((int) ($user['ref_emp'] ?? -1));
    }

    private function cleanMargin(string $key, float $default, float $min, float $max): float
    {
        $value = $this->request->getPost($key);
        if (!is_numeric($value)) return $default;
        return round(max($min, min($max, (float) $value)), 2);
    }

    private function cleanFooterHeight(): int
    {
        $value = $this->request->getPost('footer_height');
        if (!is_numeric($value)) return 28;
        return max(28, min(1200, (int) round((float) $value)));
    }

    private function cleanEditorHtml(string $html): string
    {
        $allowedTags = ['p','br','b','strong','i','em','u','ul','ol','li','div','h1','h2','h3','h4','table','thead','tbody','tfoot','tr','td','th','img','span','font'];
        $allowedFields = ['document_number','page_number','total_pages','user','requestor_name','form_date','department','mobile','address','email','employee','signature_name','verified_by','approved_by'];
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<!DOCTYPE html><html><body><div id="editor-root">' . $html . '</div></body></html>', LIBXML_NONET | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $root = $dom->getElementById('editor-root');
        if (!$root) return '';
        $clean = function (\DOMNode $node) use (&$clean, $allowedTags, $allowedFields): void {
            if ($node instanceof \DOMElement) {
                $tag = strtolower($node->tagName);
                if (!in_array($tag, $allowedTags, true)) {
                    if (in_array($tag, ['script','style','iframe','object','svg','math'], true)) { $node->parentNode?->removeChild($node); return; }
                    $parent = $node->parentNode;
                    if ($parent) while ($node->firstChild) $parent->insertBefore($node->firstChild, $node);
                    $parent?->removeChild($node);
                    return;
                }
                $attributes = [];
                foreach ($node->attributes as $attribute) $attributes[] = $attribute->name;
                foreach ($attributes as $name) {
                    $value = $node->getAttribute($name);
                    if ($name === 'style') {
                        $safe = [];
                        foreach (explode(';', $value) as $declaration) {
                            if (preg_match('/^\s*text-align\s*:\s*(left|center|right|justify)\s*$/i', $declaration, $m)) $safe[] = 'text-align:' . strtolower($m[1]);
                            elseif (preg_match('/^\s*font-size\s*:\s*(\d{1,2})(px|pt)\s*$/i', $declaration, $m) && (int) $m[1] >= 8 && (int) $m[1] <= 48) $safe[] = 'font-size:' . $m[1] . strtolower($m[2]);
                            elseif (preg_match('/^\s*(color|background-color)\s*:\s*(#[0-9a-f]{3,8})\s*$/i', $declaration, $m)) $safe[] = strtolower($m[1]) . ':' . $m[2];
                            elseif (preg_match('/^\s*font-weight\s*:\s*(bold|normal)\s*$/i', $declaration, $m)) $safe[] = 'font-weight:' . strtolower($m[1]);
                            elseif (preg_match('/^\s*font-style\s*:\s*(italic|normal)\s*$/i', $declaration, $m)) $safe[] = 'font-style:' . strtolower($m[1]);
                            elseif (preg_match('/^\s*text-decoration\s*:\s*(underline|line-through|none)\s*$/i', $declaration, $m)) $safe[] = 'text-decoration:' . strtolower($m[1]);
                            elseif (preg_match('/^\s*width\s*:\s*(25|50|75|100)%\s*$/i', $declaration, $m)) $safe[] = 'width:' . $m[1] . '%';
                            elseif (preg_match('/^\s*width\s*:\s*(\d+(?:\.\d+)?)px\s*$/i', $declaration, $m)) $safe[] = 'width:' . $m[1] . 'px';
                            elseif (preg_match('/^\s*height\s*:\s*auto\s*$/i', $declaration)) $safe[] = 'height:auto';
                            elseif (preg_match('/^\s*height\s*:\s*(\d+(?:\.\d+)?)px\s*$/i', $declaration, $m)) $safe[] = 'height:' . $m[1] . 'px';
                            elseif (preg_match('/^\s*position\s*:\s*absolute\s*$/i', $declaration)) $safe[] = 'position:absolute';
                            elseif (preg_match('/^\s*z-index\s*:\s*(0|1|10)\s*$/i', $declaration, $m)) $safe[] = 'z-index:' . $m[1];
                            elseif (preg_match('/^\s*(left|top)\s*:\s*(-?\d+(?:\.\d+)?)px\s*$/i', $declaration, $m)) $safe[] = strtolower($m[1]) . ':' . $m[2] . 'px';
                        }
                        if ($safe) $node->setAttribute('style', implode(';', $safe)); else $node->removeAttribute($name);
                    } elseif ($tag === 'img' && $name === 'src') {
                        if ($value !== '[[KINECT_LOGO]]' && !preg_match('#^data:image/jpeg;base64,[A-Za-z0-9+/]+=*$#', $value)) $node->removeAttribute($name);
                    } elseif ($tag === 'img' && in_array($name, ['alt','width','height','data-page-background','draggable'], true)) {
                        if ($name === 'data-page-background' && $value !== '1') $node->removeAttribute($name);
                        if ($name === 'draggable' && !in_array($value, ['true','false'], true)) $node->removeAttribute($name);
                        if (in_array($name, ['width','height'], true) && !ctype_digit($value)) $node->removeAttribute($name);
                    } elseif ($tag === 'font' && $name === 'color') {
                        if (!preg_match('/^#[0-9a-f]{3,8}$/i', $value)) $node->removeAttribute($name);
                    } elseif ($tag === 'font' && $name === 'face') {
                        if (!preg_match('/^(Arial|Calibri|Times New Roman|Verdana|Georgia|Tahoma)$/i', $value)) $node->removeAttribute($name);
                    } elseif ($tag === 'font' && $name === 'size') {
                        if (!ctype_digit($value) || (int) $value < 1 || (int) $value > 7) $node->removeAttribute($name);
                    } elseif ($name === 'data-template-field' && in_array($value, $allowedFields, true)) {
                        continue;
                    } elseif ($name === 'data-dynamic-block' && $value === 'equipment_rows') {
                        continue;
                    } elseif ($tag === 'div' && $name === 'data-freeform-text' && $value === '1') {
                        continue;
                    } elseif (in_array($tag, ['td','th'], true) && in_array($name, ['colspan','rowspan'], true) && ctype_digit($value) && (int) $value <= 12) {
                        continue;
                    } else {
                        $node->removeAttribute($name);
                    }
                }
            }
            $children = [];
            foreach ($node->childNodes as $child) $children[] = $child;
            foreach ($children as $child) $clean($child);
        };
        foreach (iterator_to_array($root->childNodes) as $child) $clean($child);
        $result = '';
        foreach ($root->childNodes as $child) $result .= $dom->saveHTML($child);
        return $result;
    }

    private function loadAccountabilityDocumentTemplates(): array
    {
        try {
            return \Config\Database::connect('gatepass')->table('gatepass_document_templates')->orderBy('template_key')->get()->getResultArray();
        } catch (\Throwable $e) {
            log_message('warning', 'Accountability document templates are not available yet: ' . $e->getMessage());
        }
        return [];
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

    private function loadDocumentAssignments(): array
    {
        try {
            return array_column(
                \Config\Database::connect('gatepass')->table('gatepass_document_assignments')->get()->getResultArray(),
                'template_key',
                'form_type'
            );
        } catch (\Throwable $e) {
            log_message('warning', 'Document form assignments are not available yet: ' . $e->getMessage());
            return ['itaf' => 'itaf'];
        }
    }

    public function updatePassword()
    {
        $sessionUser = session()->get('logged_in');
        if (!$sessionUser) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Please sign in again to update your password.',
            ]);
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        $newPassword = (string) $this->request->getPost('new_password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');

        if ($newPassword === '' || strlen($newPassword) < 8) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Use a new password with at least 8 characters.',
            ]);
        }

        if ($newPassword !== $confirmPassword) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'The new password and confirmation do not match.',
            ]);
        }

        $model = new userManagementModel();
        $account = !empty($sessionUser['user_id'])
            ? $model->find($sessionUser['user_id'])
            : $model->findByUsername((string) ($sessionUser['username'] ?? ''));

        if (!$account || !hash_equals((string) $account['md5_password'], md5($currentPassword))) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'The current password is incorrect.',
            ]);
        }

        if (!$model->update($account['id'], [
            'username' => $account['username'],
            'ref_emp'  => $account['ref_emp'],
            'password' => $newPassword,
        ])) {
            log_message('error', 'Settings password update failed for user id ' . (int) $account['id']);
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Password could not be updated. Please try again.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Your password has been updated.',
        ]);
    }
}
