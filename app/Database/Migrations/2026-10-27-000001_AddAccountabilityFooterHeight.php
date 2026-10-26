<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAccountabilityFooterHeight extends Migration
{
    protected $DBGroup = 'gatepass';

    public function up()
    {
        $this->forge->addColumn('gatepass_document_templates', [
            'footer_height' => ['type' => 'INT', 'unsigned' => true, 'null' => true, 'default' => null],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('gatepass_document_templates', 'footer_height');
    }
}
