<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEditableAccountabilityDocumentBody extends Migration
{
    protected $DBGroup = 'gatepass';

    private const ACK = 'This acknowledges that I am accountable for the above items documented in IT AF (IT Accountability Form). I understand that I will pay for or replace the same unit in case of loss or damage due to my fault or negligence. In case of resignation, separation, or transfer, I will turn over these items before the issuance of my clearance. For any additional hardware and software protected with a license installed that do not appear on the list above, or do not have any supporting document(s) coming from Kinect Inc., it is my responsibility to face any charges or liability from any hardware and software authority or organization. Also, I will follow the rules and regulations imposed by Kinect Inc. and I will be liable for any consequences that may arise for not complying with the set of rules and regulations.';

    public function up()
    {
        $this->forge->addColumn('gatepass_document_templates', [
            'body_html' => ['type' => 'MEDIUMTEXT', 'null' => true],
        ]);

        foreach ([
            ['itaf', 'IT ACCOUNTABILITY FORM'],
            ['itaf-2', 'ACCOUNTABILITY FORM 2'],
            ['itaf-3', 'ACCOUNTABILITY FORM 3'],
            ['itaf-4', 'ACCOUNTABILITY FORM 4'],
        ] as [$key, $title]) {
            $html = $this->sampleBody($title);
            $this->db->table('gatepass_document_templates')->where('template_key', $key)->update(['body_html' => $html]);
        }
        $this->db->table('gatepass_document_templates')->whereIn('template_key', ['itaf', 'itaf-2', 'itaf-3', 'itaf-4'])->update([
            'header_text' => '<p><img src="[[KINECT_LOGO]]" alt="Kinect Inc." width="170"></p><p>Kinect Inc.</p>',
        ]);
    }

    private function sampleBody(string $title): string
    {
        $ack = htmlspecialchars(self::ACK, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return '<h1 style="text-align:center"><strong>' . $title . '</strong></h1>'
            . '<p><strong>Document No.:</strong> <span data-template-field="document_number">[Document number]</span></p>'
            . '<table><tbody>'
            . '<tr><td><strong>Employee/Accountable Personnel Name:</strong> <span data-template-field="requestor_name">[Employee name]</span></td><td><strong>Date:</strong> <span data-template-field="form_date">[Date]</span></td></tr>'
            . '<tr><td><strong>Department:</strong> <span data-template-field="department">[Department]</span></td><td><strong>Mobile:</strong> <span data-template-field="mobile">[Mobile]</span></td></tr>'
            . '<tr><td><strong>Address:</strong> <span data-template-field="address">[Address]</span></td><td><strong>Email:</strong> <span data-template-field="email">[Email]</span></td></tr>'
            . '</tbody></table>'
            . '<h2>IT Equipment Units</h2>'
            . '<table><thead><tr><th>Item No.</th><th>Description</th><th>Model/Brand</th><th>Serial Number</th><th>Employee</th><th>Remarks</th><th>Date Issued</th></tr></thead>'
            . '<tbody><tr><td colspan="7" data-dynamic-block="equipment_rows">Equipment rows appear here</td></tr></tbody></table>'
            . '<h2>Acknowledgment</h2><p>' . $ack . '</p>'
            . '<table><tbody><tr><td>____________________________<br>Signature over Printed Name<br><span data-template-field="signature_name">[Signature name]</span></td>'
            . '<td>____________________________<br>Verified by (IT Specialist)<br><span data-template-field="verified_by">[Verified by]</span></td>'
            . '<td>____________________________<br>Approved by (Supervisor/President)<br><span data-template-field="approved_by">[Approved by]</span></td></tr></tbody></table>';
    }

    public function down()
    {
        $this->forge->dropColumn('gatepass_document_templates', 'body_html');
    }
}
