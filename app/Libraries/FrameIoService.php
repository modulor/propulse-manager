<?php

namespace App\Libraries;

class FrameioService
{
  private $baseUrl;
  private $clientId;
  private $clientSecret;

  public function __construct()
  {
    $this->baseUrl = env('FRAMEIO_BASE_URL');
    $this->clientId = env('FRAMEIO_CLIENT_ID');
    $this->clientSecret = env('FRAMEIO_CLIENT_SECRET');
  }

  private function getValidAccessToken()
  {
    $refreshToken = env('FRAMEIO_REFRESH_TOKEN');

    return $this->refreshAccessToken($refreshToken);
  }

  private function refreshAccessToken($refreshToken)
  {
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => 'https://ims-na1.adobelogin.com/ims/token/v3',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => http_build_query([
        'grant_type'    => 'refresh_token',
        'client_id'     => $this->clientId,
        'client_secret' => $this->clientSecret,
        'refresh_token' => $refreshToken
      ])
    ]);

    $response = json_decode(curl_exec($curl), true);
    curl_close($curl);

    if (isset($response['access_token'])) {
      $expiresIn = $response['expires_in'] ?? 3600;

      if (isset($response['refresh_token'])) {
        $this->updateStoredRefreshToken($response['refresh_token']);
      }

      return $response['access_token'];
    }

    throw new \Exception('No se pudo refrescar el token de Frame.io');
  }

  public function makeRequest($endpoint, $method = 'GET', $data = null)
  {
    $token = $this->getValidAccessToken();

    $url = $this->baseUrl . $endpoint;
    $curl = curl_init();

    $headers = [
      'Authorization: Bearer ' . $token,
      'Content-Type: application/json',
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

    if ($httpCode === 401) {
      // Podrías re-intentar la petición una vez más aquí.
      //$this->cache->delete('frameio_access_token');      
    }

    return json_decode($response, true);
  }

  private function updateStoredRefreshToken($newToken)
  {
    echo "updateStoredRefreshToken called with: " . $newToken;
  }

  public function getAccounts()
  {
    return $this->makeRequest('/accounts');
  }

  public function getWorkspaces($accountId)
  {
    return $this->makeRequest("/accounts/{$accountId}/workspaces");
  }

  public function getProjects($accountId, $workspaceId)
  {
    return $this->makeRequest("/accounts/{$accountId}/workspaces/{$workspaceId}/projects");
  }

  public function getCurrentUser()
  {
    return $this->makeRequest('/me');
  }
}
