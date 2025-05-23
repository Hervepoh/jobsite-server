<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TVerificationCodesCreate extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id'     => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => false,
            ],
            'code'        => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'type'        => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'expires_at'  => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'created_at'  => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at'  => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_utilisateur', 't_utilisateurs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('verification_codes');
    }

    public function down()
    {
        $this->forge->dropTable('verification_codes');
    }
}
