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
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['tersedia', 'dipinjam', 'rusak', 'hilang'],
                'default'    => 'tersedia',
            ],
            'id_barang_dipinjam' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, // Bisa NULL jika barang masih tersedia
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
        $this->forge->addForeignKey('id_barang_dipinjam', 'barang_dipinjam', 'id_barang_dipinjam', 'CASCADE', 'SET NULL');
        $this->forge->createTable('barang_detail');
    }

    public function down()
    {
        $this->forge->dropTable('barang_detail');
    }
}
