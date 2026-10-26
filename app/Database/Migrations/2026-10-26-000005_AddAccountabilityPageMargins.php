<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAccountabilityPageMargins extends Migration
{
    protected $DBGroup = 'gatepass';

    public function up()
    {
        $this->forge->addColumn('gatepass_document_templates', [
            'margin_left' => ['type' => 'DECIMAL', 'constraint' => '3,2', 'default' => 0.75],
            'margin_right' => ['type' => 'DECIMAL', 'constraint' => '3,2', 'default' => 0.75],
            'margin_top' => ['type' => 'DECIMAL', 'constraint' => '3,2', 'default' => 1.50],
            'margin_bottom' => ['type' => 'DECIMAL', 'constraint' => '3,2', 'default' => 1.00],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('gatepass_document_templates', ['margin_left', 'margin_right', 'margin_top', 'margin_bottom']);
    }
}
