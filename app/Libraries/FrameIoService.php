<?php

namespace App\Libraries;

class FrameioService
{
  private $baseUrl;
  private $accessToken;

  public function __construct()
  {
    $this->baseUrl = env('FRAMEIO_BASE_URL');
    $this->accessToken = session()->get('access_token');
  }

  public function makeRequest($endpoint, $method = 'GET', $data = null)
  {
    if (!$this->accessToken) {
      throw new \Exception('Token de acceso no disponible');
    }

    $url = $this->baseUrl . $endpoint;

    $curl = curl_init();
    $headers = [
      'Authorization: Bearer ' . $this->accessToken,
      'Content-Type: application/json'
    ];

    curl_setopt_array($curl, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_CUSTOMREQUEST => $method
    ]);

    if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpCode >= 200 && $httpCode < 300) {
      return json_decode($response, true);
    }

    throw new \Exception('Error en API: ' . $response, $httpCode);
  }

  // Obtener cuentas del usuario
  public function getAccounts()
  {
    return $this->makeRequest('/accounts');
  }

  // Obtener workspaces de una cuenta
  public function getWorkspaces($accountId)
  {
    return $this->makeRequest("/accounts/{$accountId}/workspaces");
  }

  // Obtener proyectos de un workspace
  public function getProjects($accountId, $workspaceId)
  {
    return $this->makeRequest("/accounts/{$accountId}/workspaces/{$workspaceId}/projects");
  }

  // Obtener información del usuario actual
  public function getCurrentUser()
  {
    return $this->makeRequest('/me');
  }
}
