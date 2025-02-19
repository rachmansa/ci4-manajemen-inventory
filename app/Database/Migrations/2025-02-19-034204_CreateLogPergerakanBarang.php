<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogPergerakanBarang extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
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
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'Barang Masuk', 'Barang Keluar', 'Batal Barang Keluar', 
                    'Barang Lab', 'Barang Pegawai Unit', 'Peminjaman'
                ],
                'default'    => 'Barang Keluar',
            ],
            'posisi_sebelumnya' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'posisi_sekarang' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tanggal' => [
                'type'    => 'DATETIME'
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_barang', 'barang', 'id_barang', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_barang_detail', 'barang_detail', 'id_barang_detail', 'SET NULL', 'CASCADE');
        $this->forge->createTable('log_pergerakan_barang');
    }

    public function down()
    {
        $this->forge->dropTable('log_pergerakan_barang');
    }
}
