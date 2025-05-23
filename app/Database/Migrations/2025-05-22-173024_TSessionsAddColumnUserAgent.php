<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TSessionsAddColumnUserAgent extends Migration
{
      public function up()
    {
        $fields = [
            'user_agent' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
        ];
        $this->forge->addColumn('t_sessions', $fields);
    }

    public function down()
    {
          $this->forge->dropColumn('t_sessions', 'user_agent');
    }
}
