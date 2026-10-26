<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFifthAccountabilityTemplate extends Migration
{
    protected $DBGroup = 'gatepass';
    public function up()
    {
        $source = $this->db->table('gatepass_document_templates')->where('template_key', 'itaf-4')->get()->getRowArray();
        if (!$source) return;
        unset($source['id']);
        $source['template_key'] = 'itaf-5';
        $source['template_name'] = 'Accountability Form 5';
        $source['document_title'] = 'ACCOUNTABILITY FORM 5';
        $source['body_html'] = str_replace(
            ['<h1 style="text-align:center"><strong>ACCOUNTABILITY FORM 4</strong></h1>', '<h2>IT Equipment Units</h2>'],
            ['<h1 style="text-align:left"><strong>ACCOUNTABILITY FORM 5</strong></h1><p>Equipment custody record</p><h2>Accountable employee</h2>', '<h2>Issued assets</h2>'],
            (string) ($source['body_html'] ?? '')
        );
        $source['updated_at'] = date('Y-m-d H:i:s');
        if (!$this->db->table('gatepass_document_templates')->where('template_key', 'itaf-5')->countAllResults()) $this->db->table('gatepass_document_templates')->insert($source);
    }
    public function down() { $this->db->table('gatepass_document_templates')->where('template_key', 'itaf-5')->delete(); }
}
