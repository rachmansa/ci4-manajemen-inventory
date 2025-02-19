<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBarangDetailTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_barang_detail' => [
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
            'serial_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'unique'     => true,
                'null'       => true, // Bisa null jika barang tidak memiliki serial number
            ],
            'merk' => [
                'type'       => 'VARCHAR',
                'constraint' => 255
            ],
            'posisi_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 255
            ],
            'id_jenis_penggunaan' => [
                'type'       => 'INT',
                'constraint' => 11
            ],
            'nomor_bmn' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true, // Bisa dikosongkan
            ],
            'tahun_barang' => [
                'type'       => 'YEAR',
                'null'       => false
            
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['tersedia', 'terpakai','dipinjam', 'menunggu diperbaiki', 'hilang','penghapusan aset'],
                'default'    => 'tersedia',
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

        $this->forge->addPrimaryKey('id_barang_detail');
        $this->forge->addForeignKey('id_barang', 'barang', 'id_barang', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_posisi', 'posisi_barang', 'id_posisi', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_jenis_penggunaan', 'jenis_penggunaan', 'id_penggunaan', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('barang_detail');
    }

    public function down()
    {
        $this->forge->dropTable('barang_detail');
    }
}
