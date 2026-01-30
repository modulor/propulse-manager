<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTaskStatusTable extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'id'          => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
      'name'        => ['type' => 'VARCHAR', 'constraint' => 50],
      'slug'        => ['type' => 'VARCHAR', 'constraint' => 50],
      'color_hex'   => ['type' => 'VARCHAR', 'constraint' => 7, 'null' => true],
    ]);
    $this->forge->addKey('id', true);
    $this->forge->createTable('task_status');
  }

  public function down()
  {
    $this->forge->dropTable('task_status');
  }
}
