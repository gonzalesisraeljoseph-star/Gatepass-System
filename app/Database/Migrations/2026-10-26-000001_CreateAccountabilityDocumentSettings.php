<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccountabilityDocumentSettings extends Migration
{
    protected $DBGroup = 'gatepass';

    public function up()
    {
        $this->forge->addField([
            'setting_key' => ['type' => 'VARCHAR', 'constraint' => 80],
            'setting_value' => ['type' => 'TEXT'],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('setting_key', true);
        $this->forge->createTable('gatepass_document_settings');
    }

    public function down()
    {
        $this->forge->dropTable('gatepass_document_settings', true);
    }
}
