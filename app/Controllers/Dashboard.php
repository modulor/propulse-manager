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

    $user = $frameio->getCurrentUser();

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

    return view('workspaces', $data);
  }

  public function workspace($accountId, $workspaceId)
  {
    $frameio = new FrameioService();
    $projects = $frameio->getProjects($accountId, $workspaceId);

    $data = [
      'account_id' => $accountId,
      'workspace_id' => $workspaceId,
      'projects' => $projects['data'] ?? []
    ];

    return view('workspace', $data);
  }

  public function project($accountId, $workspaceId, $projectId)
  {
    $frameio = new FrameioService();
    $project = $frameio->getProject($accountId, $projectId);

    $data = [
      'project' => $project ?? [],
      'account_id' => $accountId,
      'workspace_id' => $workspaceId,
      'project_id' => $projectId,
    ];

    return view('project', $data);
  }

  public function folders($accountId, $rootFolderId)
  {
    $frameio = new FrameioService();
    $folders = $frameio->getFolders($accountId, $rootFolderId);

    $data = [
      'account_id' => $accountId,
      'root_folder_id' => $rootFolderId,
      'folders' => $folders['data'] ?? []
    ];

    return view('folders', $data);
  }
}
