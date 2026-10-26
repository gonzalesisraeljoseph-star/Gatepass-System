<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccountabilityDocumentAssignments extends Migration
{
    protected $DBGroup = 'gatepass';

    public function up()
    {
        $this->forge->addField([
            'form_type' => ['type' => 'VARCHAR', 'constraint' => 24],
            'template_key' => ['type' => 'VARCHAR', 'constraint' => 40],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('form_type', true);
        $this->forge->addKey('template_key');
        $this->forge->createTable('gatepass_document_assignments');

        $template = $this->db->table('gatepass_document_templates')->select('template_key')
            ->where('is_assigned', 1)->get()->getRowArray();
        $this->db->table('gatepass_document_assignments')->insert([
            'form_type' => 'itaf',
            'template_key' => $template['template_key'] ?? 'itaf',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('gatepass_document_assignments', true);
    }
}
