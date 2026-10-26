<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AssignAccountabilityDocumentTemplate extends Migration
{
    protected $DBGroup = 'gatepass';

    public function up()
    {
        $this->forge->addColumn('gatepass_document_templates', [
            'is_assigned' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ]);

        $hasAssignment = $this->db->table('gatepass_document_templates')->where('is_assigned', 1)->countAllResults() > 0;
        if (!$hasAssignment) {
            $this->db->table('gatepass_document_templates')->where('template_key', 'itaf')->update(['is_assigned' => 1]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('gatepass_document_templates', 'is_assigned');
    }
}
