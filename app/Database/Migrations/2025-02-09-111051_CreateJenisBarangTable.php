<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJenisBarangTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_jenis' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'nama_jenis' => [
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
        $this->forge->addKey('id_jenis', true);
        $this->forge->createTable('jenis_barang');
    }

    public function down()
    {
        $this->forge->dropTable('jenis_barang');
    }
}
