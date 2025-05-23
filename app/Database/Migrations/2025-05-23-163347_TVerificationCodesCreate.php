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
        $this->forge->addForeignKey('user_id', 't_utilisateurs', 'id_utilisateur', 'CASCADE', 'CASCADE');
        $this->forge->createTable('t_verification_codes');
    }

    public function down()
    {
        $this->forge->dropTable('t_verification_codes');
    }
}
