<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSatuanBarangTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_satuan' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_satuan' => [
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
        $this->forge->addKey('id_satuan', true);
        $this->forge->createTable('satuan_barang');
    }

    public function down()
    {
        $this->forge->dropTable('satuan_barang');
    }
}
