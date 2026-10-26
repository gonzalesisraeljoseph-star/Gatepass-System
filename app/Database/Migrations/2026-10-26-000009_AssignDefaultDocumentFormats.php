<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AssignDefaultDocumentFormats extends Migration
{
    protected $DBGroup = 'gatepass';

    public function up()
    {
        $defaults = [
            'itaf' => 'itaf',
            'byod' => 'itaf-2',
            'egp' => 'itaf-3',
            'rrf' => 'itaf-4',
        ];

        foreach ($defaults as $formType => $templateKey) {
            $exists = $this->db->table('gatepass_document_assignments')->where('form_type', $formType)->countAllResults() > 0;
            $templateExists = $this->db->table('gatepass_document_templates')->where('template_key', $templateKey)->countAllResults() > 0;
            if (!$exists && $templateExists) {
                $this->db->table('gatepass_document_assignments')->insert([
                    'form_type' => $formType,
                    'template_key' => $templateKey,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }

    public function down()
    {
        $this->db->table('gatepass_document_assignments')->whereIn('form_type', ['byod', 'egp', 'rrf'])->delete();
    }
}
