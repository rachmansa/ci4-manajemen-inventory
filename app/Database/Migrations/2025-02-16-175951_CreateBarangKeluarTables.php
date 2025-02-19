<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BarangKeluar extends Migration
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
                'type'       => 'TEXT', // Menyimpan multiple ID barang detail (jika ada)
                'null'       => true,
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 10,
            ],
            'tanggal_keluar' => [
                'type' => 'DATE',
            ],
            'alasan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'penerima_barang' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_barang_keluar', true);
        $this->forge->addForeignKey('id_barang', 'barang', 'id_barang', 'CASCADE', 'CASCADE');
        $this->forge->createTable('barang_keluar');
    }

    public function down()
    {
        $this->forge->dropTable('barang_keluar');
    }
}
