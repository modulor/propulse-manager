<?php

namespace App\Controllers;

use App\Libraries\FrameioService;
use CodeIgniter\Controller;

class Dashboard extends Controller
{
  public function index()
  {
    if (!session()->get('access_token')) {
      return redirect()->to('/auth/login');
    }

    try {
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
    } catch (\Exception $e) {
      return redirect()->to('/')->with('error', 'Error al conectar con Frame.io: ' . $e->getMessage());
    }
  }

  public function workspaces($accountId)
  {
    if (!session()->get('access_token')) {
      return redirect()->to('/auth/login');
    }

    try {
      $frameio = new FrameioService();
      $workspaces = $frameio->getWorkspaces($accountId);

      $data = [
        'account_id' => $accountId,
        'workspaces' => $workspaces['data'] ?? []
      ];

      return view('workspaces', $data);
    } catch (\Exception $e) {
      return redirect()->to('/dashboard')->with('error', 'Error: ' . $e->getMessage());
    }
  }
}
