<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRegistrosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'usuario_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'hora_entrada' => [
                'type' => 'DATETIME',
            ],
            'hora_salida' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'duracion' => [
                'type' => 'FLOAT',
                'null' => true,
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

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('usuario_id', 'usuarios', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('registros');
    }

    public function down()
    {
        $this->forge->dropTable('registros');
    }
}
