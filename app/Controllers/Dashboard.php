<?php

namespace App\Controllers;

use App\Libraries\FrameioService;
use CodeIgniter\Controller;

class Dashboard extends Controller
{
  public function index()
  {
    echo "dashboard";

    $frameio = new FrameioService();

    // Obtener información del usuario
    $user = $frameio->getCurrentUser();

    // Obtener cuentas
    $accounts = $frameio->getAccounts();

    $data = [
      'user' => $user,
      'accounts' => $accounts['data'] ?? []
    ];

    return view('dashboard', $data);
  }

  public function workspaces($accountId)
  {
    $frameio = new FrameioService();
    $workspaces = $frameio->getWorkspaces($accountId);

    $data = [
      'account_id' => $accountId,
      'workspaces' => $workspaces['data'] ?? []
    ];
  }

  public function workspace($accountId, $workspaceId)
  {
    $frameio = new FrameioService();
    $projects = $frameio->getProjects($accountId, $workspaceId);

    $data = [
      'workspace_id' => $workspaceId,
      'projects' => $projects['data'] ?? []
    ];

    echo "<pre>";
    print_r($data);
    echo "</pre>";
  }
}
