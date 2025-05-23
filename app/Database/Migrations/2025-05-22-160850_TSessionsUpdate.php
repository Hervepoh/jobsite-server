<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TSessionsUpdate extends Migration
{
    public function up()
    {
        $fields = [
            'user_id' => ['type' => 'INT'],
        ];
        $this->forge->addColumn('t_sessions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('t_sessions', 'user_id');
    }
}
