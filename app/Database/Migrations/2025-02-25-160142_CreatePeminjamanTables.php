<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePeminjamanTables extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_peminjaman' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'pegawai_id' => [
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
            ],
            'tanggal_peminjaman' => [
                'type' => 'DATETIME',
            ],
            'tanggal_pengembalian' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'kondisi_awal' => [
                'type'       => 'ENUM',
                'constraint' => ['baik', 'rusak', 'hilang'],
                'default'    => 'Baik',
            ],
            'kondisi_akhir' => [
                'type'       => 'ENUM',
                'constraint' => ['baik', 'rusak', 'hilang'],
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Dipinjam', 'Dikembalikan'],
                'default'    => 'Dipinjam',
            ],
        ]);
        
        $this->forge->addPrimaryKey('id_peminjaman');
        $this->forge->addForeignKey('pegawai_id', 'pegawai_unit', 'id_pegawai_unit', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_barang', 'barang', 'id_barang', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_barang_detail', 'barang_detail', 'id_barang_detail', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('peminjaman');
    }

    public function down()
    {
        $this->forge->dropTable('peminjaman');
    }
}
