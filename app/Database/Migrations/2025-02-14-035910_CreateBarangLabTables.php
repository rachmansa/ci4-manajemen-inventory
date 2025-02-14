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
                'auto_increment' => true
            ],
            'id_barang_detail' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'serial_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'nomor_bmn' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'id_lab' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'nama_barang_lab' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'id_jenis_penggunaan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 2, // Hardcoded ke "Lab CAT"
            ],
            'kondisi' => [
                'type'       => 'ENUM',
                'constraint' => ['Baik', 'Rusak', 'Diperbaiki'],
                'default'    => 'Baik',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);

        // Primary Key
        $this->forge->addPrimaryKey('id_barang_lab');

        // Foreign Keys
        $this->forge->addForeignKey('id_barang_detail', 'barang_detail', 'id_barang_detail', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_lab', 'lab_cat', 'id_lab', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_jenis_penggunaan', 'jenis_penggunaan', 'id_penggunaan', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey('id_barang_detail'); // Mencegah duplikasi
        
        // Buat tabel
        $this->forge->createTable('barang_lab');
    }

    public function down()
    {
        $this->forge->dropTable('barang_lab');
    }
}
