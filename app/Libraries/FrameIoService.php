<?php

namespace App\Libraries;

use App\Models\FrameioTokenModel;
use CodeIgniter\Encryption\Exceptions\EncryptionException;

class FrameioService
{
  private $baseUrl;
  private $clientId;
  private $clientSecret;
  private $encrypter;
  private $tokenModel;
  private $targetEmail = 'hola@magianegravfx.com'; // Cuenta maestra

  public function __construct()
  {
    $this->baseUrl      = env('FRAMEIO_BASE_URL', 'https://api.frame.io/v4');
    $this->clientId     = env('FRAMEIO_CLIENT_ID');
    $this->clientSecret = env('FRAMEIO_CLIENT_SECRET');
    $this->encrypter    = \Config\Services::encrypter();
    $this->tokenModel   = new FrameioTokenModel();
  }

  /**
   * Realiza una petición a la API manejando la renovación de tokens automáticamente
   */
  public function makeRequest($endpoint, $method = 'GET', $data = null)
  {
    $token = $this->getValidAccessToken();

    $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');
    $curl = curl_init();

    $headers = [
      'Authorization: Bearer ' . $token,
      'Content-Type: application/json',
      'x-api-version: 2024-01-01' // Requerido para API v4
    ];

    curl_setopt_array($curl, [
      CURLOPT_URL            => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER     => $headers,
      CURLOPT_CUSTOMREQUEST  => $method,
      CURLOPT_TIMEOUT        => 30,
    ]);

    if ($data && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $decodedResponse = json_decode($response, true);

    // Si el token falló (401), invalidamos el token actual e intentamos una vez más
    if ($httpCode === 401) {
      log_message('debug', 'Token expirado detectado en petición. Intentando refrescar...');
      return $this->retryAfterRefresh($endpoint, $method, $data);
    }

    if ($httpCode >= 200 && $httpCode < 300) {
      return $decodedResponse;
    }

    throw new \Exception("Error en API Frame.io ({$httpCode}): " . ($decodedResponse['message'] ?? $response));
  }


  public function saveTokens($refreshToken, $accessToken = null, $expiresIn = null)
  {
    $data = [
      'account_email' => $this->targetEmail,
      'refresh_token' => base64_encode($this->encrypter->encrypt($refreshToken)),
      'updated_at'    => date('Y-m-d H:i:s'),
    ];

    if ($accessToken) {
      $data['access_token'] = base64_encode($this->encrypter->encrypt($accessToken));
      $data['expires_at']   = date('Y-m-d H:i:s', time() + $expiresIn);
    }

    $existing = $this->tokenModel->where('account_email', $this->targetEmail)->first();

    if ($existing) {
      return $this->tokenModel->update($existing['id'], $data);
    }

    return $this->tokenModel->insert($data);
  }
  /**
   * Lógica para obtener el token: de DB si es válido, o refrescarlo si expiró
   */
  private function getValidAccessToken()
  {
    $record = $this->tokenModel->where('account_email', $this->targetEmail)->first();

    if (!$record) {
      throw new \Exception('No hay credenciales configuradas. Realiza el Login Único primero.');
    }

    // Si el access_token aún es válido (damos un margen de 1 minuto)
    if (!empty($record['access_token']) && strtotime($record['expires_at']) > (time() + 60)) {
      return $this->decryptValue($record['access_token']);
    }

    // Si no es válido, usamos el refresh_token para obtener uno nuevo
    $refreshToken = $this->decryptValue($record['refresh_token']);
    return $this->refreshAndSaveTokens($refreshToken);
  }

  /**
   * Intercambia el Refresh Token por un nuevo par de tokens en Adobe IMS
   */
  private function refreshAndSaveTokens($refreshToken)
  {
    $client = \Config\Services::curlrequest();

    try {
      $response = $client->post('https://ims-na1.adobelogin.com/ims/token/v3', [
        'form_params' => [
          'grant_type'    => 'refresh_token',
          'client_id'     => $this->clientId,
          'client_secret' => $this->clientSecret,
          'refresh_token' => $refreshToken
        ]
      ]);

      $data = json_decode($response->getBody(), true);

      if (!isset($data['access_token'])) {
        throw new \Exception('Adobe no devolvió un access_token válido.');
      }

      // Actualizamos la base de datos con los nuevos valores
      $update = [
        'access_token' => $this->encryptValue($data['access_token']),
        'expires_at'   => date('Y-m-d H:i:s', time() + $data['expires_in']),
      ];

      // Adobe puede rotar el refresh_token (enviarte uno nuevo), lo guardamos si viene
      if (isset($data['refresh_token'])) {
        $update['refresh_token'] = $this->encryptValue($data['refresh_token']);
      }

      $this->tokenModel->where('account_email', $this->targetEmail)->set($update)->update();

      return $data['access_token'];
    } catch (\Exception $e) {
      log_message('error', 'Error crítico refrescando token de Frame.io: ' . $e->getMessage());
      throw new \Exception('La conexión con Frame.io se ha perdido. Es necesario re-autenticar la cuenta maestra.');
    }
  }

  /**
   * Caso de borde: Si el token parecía válido pero la API devolvió 401
   */
  private function retryAfterRefresh($endpoint, $method, $data)
  {
    $record = $this->tokenModel->where('account_email', $this->targetEmail)->first();
    $refreshToken = $this->decryptValue($record['refresh_token']);

    // Forzamos el refresco
    $this->refreshAndSaveTokens($refreshToken);

    // Reintentamos la petición original
    return $this->makeRequest($endpoint, $method, $data);
  }

  // --- MÉTODOS DE UTILIDAD PARA EL API ---

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

  // --- ENCRIPTACIÓN ---

  private function encryptValue($value)
  {
    return base64_encode($this->encrypter->encrypt($value));
  }

  private function decryptValue($value)
  {
    try {
      return $this->encrypter->decrypt(base64_decode($value));
    } catch (\Exception $e) {
      throw new \Exception('Error al desencriptar el token. Verifica la encryption.key.');
    }
  }
}
