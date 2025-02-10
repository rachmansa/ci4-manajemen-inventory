<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLabTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_lab' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'nama_lab' => [
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
        $this->forge->addKey('id_lab', true);
        $this->forge->createTable('lab_cat');
    }

    public function down()
    {
        $this->forge->dropTable('lab_cat');
    }
}
