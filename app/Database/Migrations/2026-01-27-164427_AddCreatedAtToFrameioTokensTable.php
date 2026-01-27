<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCreatedAtToFrameioTokensTable extends Migration
{
  public function up()
  {
    $this->forge->addColumn('frameio_tokens', [
      'created_at' => [
        'type' => 'DATETIME',
        'null' => true,
      ],
    ]);
  }

  public function down()
  {
    $this->forge->dropColumn('frameio_tokens', 'created_at');
  }
}
