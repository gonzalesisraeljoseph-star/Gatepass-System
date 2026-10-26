<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccountabilityDocumentTemplates extends Migration
{
    protected $DBGroup = 'gatepass';

    private const ACKNOWLEDGMENT = 'This acknowledges that I am accountable for the above items documented in IT AF (IT Accountability Form). I understand that I will pay for or replace the same unit in case of loss or damage due to my fault or negligence. In case of resignation, separation, or transfer, I will turn over these items before the issuance of my clearance. For any additional hardware and software protected with a license installed that do not appear on the list above, or do not have any supporting document(s) coming from Kinect Inc., it is my responsibility to face any charges or liability from any hardware and software authority or organization. Also, I will follow the rules and regulations imposed by Kinect Inc. and I will be liable for any consequences that may arise for not complying with the set of rules and regulations.';

    public function up()
    {
        $this->forge->addField([
            'template_key' => ['type' => 'VARCHAR', 'constraint' => 40],
            'template_name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'header_text' => ['type' => 'TEXT'],
            'document_title' => ['type' => 'VARCHAR', 'constraint' => 180],
            'equipment_heading' => ['type' => 'VARCHAR', 'constraint' => 180],
            'equipment_headers' => ['type' => 'TEXT'],
            'acknowledgment' => ['type' => 'TEXT'],
            'footer_text' => ['type' => 'TEXT'],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('template_key', true);
        $this->forge->createTable('gatepass_document_templates');

        $header = 'Kinect Inc.';
        $footer = "www.kinectinc.com\n632 5310-5405\nUnit 602 6F Panorama Technocenter, 1029 EDSA, Veterans Village, Quezon City, Metro Manila, Philippines, 1105";
        foreach ([
            ['itaf', 'IT Accountability Form', 'IT ACCOUNTABILITY FORM'],
            ['itaf-2', 'Accountability Form 2', 'ACCOUNTABILITY FORM 2'],
            ['itaf-3', 'Accountability Form 3', 'ACCOUNTABILITY FORM 3'],
            ['itaf-4', 'Accountability Form 4', 'ACCOUNTABILITY FORM 4'],
        ] as [$key, $name, $title]) {
            $this->db->table('gatepass_document_templates')->insert([
                'template_key' => $key,
                'template_name' => $name,
                'header_text' => $header,
                'document_title' => $title,
                'equipment_heading' => 'IT Equipment Units',
                'equipment_headers' => json_encode(['Item No.', 'Description', 'Model/Brand', 'Serial Number', 'Employee', 'Remarks', 'Date Issued']),
                'acknowledgment' => self::ACKNOWLEDGMENT,
                'footer_text' => $footer,
            ]);
        }

        // Carry forward values saved by the earlier single-format settings page.
        if ($this->db->tableExists('gatepass_document_settings')) {
            $legacy = array_column($this->db->table('gatepass_document_settings')->get()->getResultArray(), 'setting_value', 'setting_key');
            $carry = array_intersect_key($legacy, array_flip(['document_title', 'equipment_heading', 'acknowledgment']));
            if ($carry) $this->db->table('gatepass_document_templates')->where('template_key', 'itaf')->update($carry);
            if (!empty($legacy['company_name'])) $this->db->table('gatepass_document_templates')->where('template_key', 'itaf')->update(['header_text' => $legacy['company_name']]);
        }

        $this->forge->addColumn('gatepass_requests', [
            'accountability_template_key' => ['type' => 'VARCHAR', 'constraint' => 40, 'default' => 'itaf'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('gatepass_requests', 'accountability_template_key');
        $this->forge->dropTable('gatepass_document_templates', true);
    }
}
