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
            'id_barang_detail' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, // Bisa NULL jika barang belum punya detail
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
            'tanggal_serah_terima_awal' => [
                'type' => 'DATE',
            ],
            'tanggal_serah_terima_akhir' => [
                'type' => 'DATE',
                'null' => true, // Bisa NULL jika belum ada batas akhir
            ],
            'status_barang' => [
                'type'       => 'ENUM',
                'constraint' => ['Baik', 'Rusak', 'Hilang'],
                'default'    => 'Baik', // Default barang dalam kondisi baik
            ],
            'keterangan' => [
                'type'       => 'TEXT',
                'null'       => true,
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

        $this->forge->addKey('id_barang_pegawai_unit', true);
        $this->forge->addForeignKey('id_barang', 'barang', 'id_barang', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_barang_detail', 'barang_detail', 'id_barang_detail', 'SET NULL', 'CASCADE'); // Jika barang detail dihapus, set NULL
        $this->forge->addForeignKey('id_pegawai_unit', 'pegawai_unit', 'id_pegawai_unit', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_jenis_penggunaan', 'jenis_penggunaan', 'id_penggunaan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('barang_pegawai_unit');
    }

    public function down()
    {
        $this->forge->dropTable('barang_pegawai_unit');
    }
}
