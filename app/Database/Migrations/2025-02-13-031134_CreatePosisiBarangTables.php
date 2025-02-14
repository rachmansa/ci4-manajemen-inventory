<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePosisiBarangTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_posisi' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'auto_increment' => true,
            ],
            'nama_posisi' => [
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
        $this->forge->addKey('id_posisi', true);
        $this->forge->createTable('posisi_barang');
    }

    public function down()
    {
        $this->forge->dropTable('posisi_barang');
    }
}
