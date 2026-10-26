<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAccountabilityRequests extends Migration
{
    protected $DBGroup = 'gatepass';

    public function up()
    {
        $this->forge->addColumn('gatepass_requests', [
            'request_type' => ['type' => 'VARCHAR', 'constraint' => 32, 'default' => 'gatepass'],
            'department' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'mobile' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'address' => ['type' => 'TEXT', 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'form_date' => ['type' => 'DATE', 'null' => true],
            'signature_name' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'verified_by' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'approved_by' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'acknowledgment_accepted' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ]);

        $this->forge->addField([
            'id' => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'request_id' => ['type' => 'INT', 'unsigned' => true],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255],
            'model_brand' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'serial_number' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'remarks' => ['type' => 'TEXT', 'null' => true],
            'item_date' => ['type' => 'DATE', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('request_id');
        $this->forge->createTable('gatepass_accountability_items');

    }

    public function down()
    {
        $this->forge->dropTable('gatepass_accountability_items', true);
        $this->forge->dropColumn('gatepass_requests', ['request_type', 'department', 'mobile', 'address', 'email', 'form_date', 'signature_name', 'verified_by', 'approved_by', 'acknowledgment_accepted']);
    }
}
