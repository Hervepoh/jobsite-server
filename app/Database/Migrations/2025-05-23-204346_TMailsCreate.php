<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TMailsCreate extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_mail'         => ['type' => 'INT', 'auto_increment' => true],
            'to'              => ['type' => 'VARCHAR', 'constraint' => 255],
            'subject'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'content'         => ['type' => 'TEXT'],
            'send'            => ['type' => 'TINYINT', 'default' => 0], // 0: pending, 1: sent, 2: failed
            'send_at' => ['type' => 'DATETIME', 'null' => true],
            'retry_count'     => ['type' => 'TINYINT', 'default' => 0],
            'error_message'   => ['type' => 'TEXT', 'null' => true],
            'last_attempt_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id_mail', true);
        $this->forge->createTable('t_mails');
    }

    public function down()
    {
        $this->forge->dropTable('t_mails');
    }
}
