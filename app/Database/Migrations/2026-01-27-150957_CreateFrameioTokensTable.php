<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFrameioTokensTable extends Migration
{
  public function up()
  {
    $this->forge->addField([
      'id' => [
        'type'           => 'INT',
        'constraint'     => 5,
        'unsigned'       => true,
        'auto_increment' => true,
      ],
      'account_email' => [
        'type'       => 'VARCHAR',
        'constraint' => '100',
        'unique'     => true,
      ],
      'refresh_token' => [
        'type' => 'TEXT',
      ],
      'access_token' => [
        'type' => 'TEXT',
        'null' => true,
      ],
      'expires_at' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
      'updated_at' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
    ]);
    $this->forge->addKey('id', true);
    $this->forge->createTable('frameio_tokens');
  }

  public function down()
  {
    $this->forge->dropTable('frameio_tokens');
  }
}
