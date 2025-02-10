<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBarangPegawaiUnitTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_barang_pegawai_unit' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_barang' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_pegawai_unit' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'id_jenis_penggunaan' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'tanggal_serah_terima' => [
                'type' => 'DATE',
            ],
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true,
            ]
        ]);

        $this->forge->addKey('id_barang_pegawai_unit', true);
        $this->forge->addForeignKey('id_barang', 'barang', 'id_barang', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_pegawai_unit', 'pegawai_unit', 'id_pegawai_unit', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_jenis_penggunaan', 'jenis_penggunaan', 'id_penggunaan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('barang_pegawai_unit');
    }

    public function down()
    {
        $this->forge->dropTable('barang_pegawai_unit');
    }
}
