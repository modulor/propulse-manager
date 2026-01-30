<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTasksTable extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
      'description'      => ['type' => 'TEXT'],
      'assigned_to'      => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
      'status_id'        => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true],
      'shot_id'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
      'shot_url'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
      'reference_url'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
      'assets_url'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
      'render_url'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
      'created_by'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
      'created_at'       => ['type' => 'DATETIME', 'null' => true],
      'updated_at'       => ['type' => 'DATETIME', 'null' => true],
    ]);

    $this->forge->addKey('id', true);

    $this->forge->addForeignKey('assigned_to', 'users', 'id', 'CASCADE', 'RESTRICT');
    $this->forge->addForeignKey('status_id', 'task_status', 'id', 'CASCADE', 'RESTRICT');
    $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'RESTRICT');

    $this->forge->createTable('tasks');
  }

  public function down()
  {
    $this->forge->dropTable('tasks');
  }
}
