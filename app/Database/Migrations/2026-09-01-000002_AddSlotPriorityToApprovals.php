<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSlotPriorityToApprovals extends Migration
{
    protected $DBGroup = 'gatepass';

    public function up()
    {
        if (! $this->db->fieldExists('slot_priority', 'gatepass_request_approvals')) {
            $this->forge->addColumn('gatepass_request_approvals', [
                'slot_priority' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'after'      => 'role_id',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('slot_priority', 'gatepass_request_approvals')) {
            $this->forge->dropColumn('gatepass_request_approvals', 'slot_priority');
        }
    }
}
