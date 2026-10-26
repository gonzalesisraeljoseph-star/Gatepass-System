<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAccountabilityDatesAndAttachments extends Migration
{
    protected $DBGroup = 'gatepass';

    public function up()
    {
        $this->forge->addColumn('gatepass_requests', [
            'accountability_type' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'start_date' => ['type' => 'DATE', 'null' => true],
            'end_date' => ['type' => 'DATE', 'null' => true],
            'no_days' => ['type' => 'INT', 'constraint' => 10, 'null' => true],
        ]);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'request_id' => ['type' => 'INT', 'unsigned' => true],
            'original_name' => ['type' => 'VARCHAR', 'constraint' => 255],
            'stored_name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'mime_type' => ['type' => 'VARCHAR', 'constraint' => 120],
            'file_size' => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('request_id');
        $this->forge->createTable('gatepass_accountability_attachments');
    }

    public function down()
    {
        $this->forge->dropTable('gatepass_accountability_attachments', true);
        $this->forge->dropColumn('gatepass_requests', ['accountability_type', 'start_date', 'end_date', 'no_days']);
    }
}
