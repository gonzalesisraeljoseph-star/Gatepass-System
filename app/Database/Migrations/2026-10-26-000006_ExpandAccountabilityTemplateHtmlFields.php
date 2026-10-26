<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ExpandAccountabilityTemplateHtmlFields extends Migration
{
    protected $DBGroup = 'gatepass';

    public function up()
    {
        $this->forge->modifyColumn('gatepass_document_templates', [
            'header_text' => ['name' => 'header_text', 'type' => 'LONGTEXT'],
            'footer_text' => ['name' => 'footer_text', 'type' => 'LONGTEXT'],
            'body_html' => ['name' => 'body_html', 'type' => 'LONGTEXT'],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('gatepass_document_templates', [
            'header_text' => ['name' => 'header_text', 'type' => 'TEXT'],
            'footer_text' => ['name' => 'footer_text', 'type' => 'TEXT'],
            'body_html' => ['name' => 'body_html', 'type' => 'MEDIUMTEXT'],
        ]);
    }
}
