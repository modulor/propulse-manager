<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
  public function run()
  {
    $roles = [
      ['rolename' => 'Admin', 'description' => 'Administrador del sistema'],
      ['rolename' => 'Artist', 'description' => 'Artista VFX / Editor'],
    ];
    $this->db->table('users_roles')->ignore(true)->insertBatch($roles);

    $status = [
      ['name' => 'Pending',     'slug' => 'pending',     'color_hex' => '#FFC107'],
      ['name' => 'In Progress', 'slug' => 'in-progress', 'color_hex' => '#007BFF'],
      ['name' => 'Review',      'slug' => 'review',      'color_hex' => '#6F42C1'],
      ['name' => 'Approved',    'slug' => 'approved',    'color_hex' => '#28A745'],
    ];
    $this->db->table('task_status')->ignore(true)->insertBatch($status);
  }
}
