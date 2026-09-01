<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSlotPriorityToApprovals extends Migration
{
    protected $DBGroup = 'gatepass';

    public function up()
    {
        $this->forge->addColumn('gatepass_request_approvals', [
            'slot_priority' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'role_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('gatepass_request_approvals', 'slot_priority');
    }
}