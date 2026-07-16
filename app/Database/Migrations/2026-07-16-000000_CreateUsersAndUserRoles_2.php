<?php
// Destination in your CI4 app: app/Database/Migrations/2026-07-16-000000_CreateUsersAndUserRoles.php
// Run with: php spark migrate

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersAndUserRoles extends Migration
{
    public function up()
    {
        // --- users table -----------------------------------------------
        // id here = ref_emp (employee id) from the source tbl_user_info table,
        // NOT tbl_user_info's own auto-increment id.
        // No auto_increment: we deliberately insert explicit id values.
        $this->forge->addField([
            'id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('users');

        // --- user_roles pivot table --------------------------------------
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'role_id' => [
                // NOT unsigned - matches roles.role_id, which is a plain signed int(11).
                // Run `SHOW CREATE TABLE roles;` to confirm before migrating.
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');

        // Makes the sync repeatable: rerunning won't duplicate a role assignment.
        $this->forge->addUniqueKey(['user_id', 'role_id']);

        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        // NOTE: table is named 'role' (singular), and its primary key is
        // 'role_id', not 'id'.
        $this->forge->addForeignKey('role_id', 'role', 'role_id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('user_roles');
    }

    public function down()
    {
        $this->forge->dropTable('user_roles');
        $this->forge->dropTable('users');
    }
}
