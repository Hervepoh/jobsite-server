<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TSessionsAddColumnStatus extends Migration
{
    public function up()
    {
        $fields = [
            'active' => [
                'type' => 'BOOLEAN',
                'default' => true
            ],
        ];
        $this->forge->addColumn('t_sessions', $fields);
    }

    public function down()
    {
          $this->forge->dropColumn('t_sessions', 'active');
    }
}
