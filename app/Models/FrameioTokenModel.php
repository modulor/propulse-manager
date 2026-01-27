<?php

namespace App\Models;

use CodeIgniter\Model;

class FrameioTokenModel extends Model
{
  protected $table            = 'frameio_tokens';
  protected $primaryKey       = 'id';
  protected $allowedFields    = ['account_email', 'refresh_token', 'access_token', 'expires_at', 'updated_at'];
  protected $useTimestamps    = true;
  protected $updatedField     = 'updated_at';
}
