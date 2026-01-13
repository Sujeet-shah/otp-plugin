<?php

namespace OtpAuth\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOtpTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'user_id' => [
                'type' => 'INT',
                'default' => NULL,
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'attempts' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 0,
            ],
            'is_verified' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        // $this->forge->addKey('phone');
        $this->forge->addKey('expires_at');
        $this->forge->createTable('otp_requests');
    }

    public function down()
    {
        $this->forge->dropTable('otp_requests');
    }
}
