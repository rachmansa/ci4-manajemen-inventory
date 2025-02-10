<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBarangTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_barang' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'stok' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'stok_minimal' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'   => 1,
            ],
            'kode_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'deskripsi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'id_satuan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_jenis' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            
        ]);

        $this->forge->addKey('id_barang', true);
        $this->forge->addForeignKey('id_satuan', 'satuan_barang', 'id_satuan', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_jenis', 'jenis_barang', 'id_jenis', 'CASCADE', 'CASCADE');
        $this->forge->createTable('barang');
    }

    public function down()
    {
        $this->forge->dropTable('barang');
    }
}
