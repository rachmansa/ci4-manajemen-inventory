<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBarangLabTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_barang_lab' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_lab' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'id_barang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_barang_detail' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, // Bisa null jika barang tidak punya SN
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_jenis_penggunaan' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id_barang_lab', true);
        $this->forge->addForeignKey('id_lab', 'lab_cat', 'id_lab', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_barang', 'barang', 'id_barang', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_barang_detail', 'barang_detail', 'id_barang_detail', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('id_jenis_penggunaan', 'jenis_penggunaan', 'id_penggunaan', 'CASCADE', 'CASCADE');

        $this->forge->createTable('barang_lab');
    }

    public function down()
    {
        $this->forge->dropTable('barang_lab');
    }
}
