<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAccountabilityDocumentNumbers extends Migration
{
    protected $DBGroup = 'gatepass';

    public function up()
    {
        $this->forge->addColumn('gatepass_requests', [
            'document_number' => ['type' => 'VARCHAR', 'constraint' => 24, 'null' => true],
        ]);
        $this->db->query('ALTER TABLE gatepass_requests ADD UNIQUE KEY uq_gatepass_document_number (document_number)');
        $this->forge->addColumn('gatepass_accountability_items', [
            'employee' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true, 'after' => 'serial_number'],
        ]);

        $this->forge->addField([
            'sequence_year' => ['type' => 'INT', 'constraint' => 4, 'unsigned' => true],
            'last_number' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'default' => 0],
        ]);
        $this->forge->addKey('sequence_year', true);
        $this->forge->createTable('gatepass_accountability_sequences');
        $this->db->table('gatepass_accountability_sequences')->insert([
            'sequence_year' => 2026,
            'last_number' => 66,
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('gatepass_accountability_sequences', true);
        $this->forge->dropColumn('gatepass_accountability_items', 'employee');
        $this->db->query('ALTER TABLE gatepass_requests DROP INDEX uq_gatepass_document_number');
        $this->forge->dropColumn('gatepass_requests', 'document_number');
    }
}
