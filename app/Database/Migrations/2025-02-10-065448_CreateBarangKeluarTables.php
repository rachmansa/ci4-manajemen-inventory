<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BarangKeluarMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_barang_keluar' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_barang' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'id_barang_detail' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
            'id_penggunaan' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 10,
            ],
            'tanggal_keluar' => [
                'type' => 'DATE',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_barang_keluar', true);
        $this->forge->addForeignKey('id_barang', 'barang', 'id_barang', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_barang_detail', 'barang_detail', 'id_barang_detail', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('id_penggunaan', 'jenis_penggunaan', 'id_penggunaan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('barang_keluar');
    }

    public function down()
    {
        $this->forge->dropTable('barang_keluar');
    }
}
