<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePegawaiUnitTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pegawai_unit' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'nip' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'nama_pegawai' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'unit_kerja' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_pegawai_unit', true);
        $this->forge->createTable('pegawai_unit');
    }

    public function down()
    {
        $this->forge->dropTable('pegawai_unit');
    }
}
