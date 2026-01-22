<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersRolesTable extends Migration
{
    public function up()
    {
        // 1. Definir los campos
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'rolename' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true, // Permitimos que sea opcional
            ],
        ]);

        // 2. Definir la llave primaria
        $this->forge->addKey('id', true);

        // 3. Crear la tabla
        $this->forge->createTable('users_roles');
    }

    public function down()
    {
        // Si revertimos la migración, borramos la tabla
        $this->forge->dropTable('users_roles');
    }
}
