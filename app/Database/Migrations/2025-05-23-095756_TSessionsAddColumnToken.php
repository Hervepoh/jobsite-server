<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TSessionsAddColumnToken extends Migration
{
    public function up()
    {
         $fields = [
            'refresh_token' => [
                'type' => 'TEXT',
                'null' => true,
                'default' => null,
            ],
            'expires_at' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
        ];
        $this->forge->addColumn('t_sessions', $fields);
    }

    public function down()
    {
          $this->forge->dropColumn('t_sessions', 'refresh_token');
          $this->forge->dropColumn('t_sessions', 'expires_at');
    }
}
