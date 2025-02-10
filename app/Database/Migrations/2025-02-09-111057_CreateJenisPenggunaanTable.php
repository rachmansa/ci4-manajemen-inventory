<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJenisPenggunaanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_penggunaan' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'nama_penggunaan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_penggunaan', true);
        $this->forge->createTable('jenis_penggunaan');
    }

    public function down()
    {
        $this->forge->dropTable('jenis_penggunaan');
    }
}
