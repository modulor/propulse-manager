<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
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
      'email' => [
        'type'       => 'VARCHAR',
        'constraint' => '255',
        'unique'     => true, // No queremos emails repetidos
      ],
      'password' => [
        'type'       => 'VARCHAR',
        'constraint' => '255',
      ],
      'users_roles_id' => [
        'type'       => 'INT',
        'constraint' => 5,
        'unsigned'   => true, // ¡Importante! Debe coincidir con el ID de users_roles
      ],
      'active' => [
        'type'       => 'TINYINT',
        'constraint' => 1,
        'default'    => 1, // 1 = Activo, 0 = Inactivo
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

    // Llave primaria
    $this->forge->addKey('id', true);

    // RELACIÓN (Foreign Key)
    // Esto crea el vínculo real en la base de datos
    $this->forge->addForeignKey('users_roles_id', 'users_roles', 'id', 'CASCADE', 'RESTRICT');

    // Crear la tabla
    $this->forge->createTable('users');
  }

  public function down()
  {
    $this->forge->dropTable('users');
  }
}
