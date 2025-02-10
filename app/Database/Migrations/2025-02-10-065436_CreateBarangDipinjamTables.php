<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BarangDipinjamMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_barang_dipinjam' => [
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
            'id_pegawai_unit' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'id_lab' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 10,
            ],
            'tanggal_pinjam' => [
                'type' => 'DATE',
            ],
            'tanggal_kembali' => [
                'type'    => 'DATE',
                'null'    => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Dipinjam', 'Dikembalikan'],
                'default'    => 'Dipinjam',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_barang_dipinjam', true);
        $this->forge->addForeignKey('id_barang', 'barang', 'id_barang', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_barang_detail', 'barang_detail', 'id_barang_detail', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('id_pegawai_unit', 'pegawai_unit', 'id_pegawai_unit', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_lab', 'lab_cat', 'id_lab', 'CASCADE', 'CASCADE');
        $this->forge->createTable('barang_dipinjam');
    }

    public function down()
    {
        $this->forge->dropTable('barang_dipinjam');
    }
}
