<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWorkflowNodeApprovers extends Migration
{
    protected $DBGroup = 'gatepass';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'node_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'priority' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'role_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('node_id');
        $this->forge->createTable('workflow_node_approvers');
    }

    public function down()
    {
        $this->forge->dropTable('workflow_node_approvers');
    }
}