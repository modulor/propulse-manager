<?php

namespace App\Controllers;

use App\Libraries\FrameIoService;

class FrameTest extends BaseController
{
  private $clientId = '406de63f9d984c1bb7b1c335350563a0';
  private $clientSecret = 'p8e-12nds2kenc99GAgXj4ZwKeTOtpMw-uKX';
  private $baseUrl = 'https://api.frame.io/v4';
  private $redirectUri = 'https://propulse-manager.test/auth/callback';

  public function getAccounts()
  {
    $accessToken = session()->get('frameio_access_token');
    if (!$accessToken) {
      return $this->response->setJSON(['error' => 'No access token. Please authenticate first.']);
    }
    $service = new FrameIoService();
    $accounts = $service->getAccounts($accessToken);
    return $this->response->setJSON($accounts);
  }

  public function initiateAuth()
  {
    // Generar state para seguridad
    $state = bin2hex(random_bytes(16));
    session()->set('oauth_state', $state);

    // Construir URL de autorización de Adobe IMS
    $authUrl = 'https://ims-na1.adobelogin.com/ims/authorize/v1?' . http_build_query([
      'client_id' => $this->clientId,
      'redirect_uri' => $this->redirectUri,
      'response_type' => 'code',
      'scope' => 'additional_info.roles,email,offline_access,openid,profile',
      'state' => $state
    ]);

    return redirect()->to($authUrl);
  }

  public function handleCallback()
  {
    $code = $this->request->getGet('code');
    $state = $this->request->getGet('state');
    $error = $this->request->getGet('error');

    // Verificar state
    if ($state !== session()->get('oauth_state')) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Invalid state parameter'
      ]);
    }

    if ($error) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'OAuth error: ' . $error
      ]);
    }

    // Intercambiar código por token
    $tokenData = $this->exchangeCodeForToken($code);

    if ($tokenData) {
      // Verificar acceso a Frame.io API
      return $this->testFrameioAccess($tokenData['access_token']);
    }

    return $this->response->setJSON([
      'success' => false,
      'message' => 'Failed to obtain access token'
    ]);
  }

  public function getProjects()
  {
    $accessToken = session()->get('frameio_access_token');

    $client = \Config\Services::curlrequest();
    $response = $client->get($this->baseUrl . '/projects', [
      'headers' => [
        'Authorization' => 'Bearer ' . $accessToken,
        'Content-Type' => 'application/json'
      ]
    ]);

    return $this->response->setJSON(json_decode($response->getBody(), true));
  }


  private function exchangeCodeForToken($code)
  {
    $client = \Config\Services::curlrequest();

    try {
      $response = $client->post('https://ims-na1.adobelogin.com/ims/token/v1', [
        'form_params' => [
          'grant_type' => 'authorization_code',
          'client_id' => $this->clientId,
          'client_secret' => $this->clientSecret,
          'code' => $code,
          'redirect_uri' => $this->redirectUri
        ],
        'headers' => [
          'Content-Type' => 'application/x-www-form-urlencoded'
        ]
      ]);

      if ($response->getStatusCode() === 200) {
        return json_decode($response->getBody(), true);
      }
    } catch (\Exception $e) {
      log_message('error', 'Token exchange error: ' . $e->getMessage());
    }

    return null;
  }

  private function testFrameioAccess($accessToken)
  {
    $client = \Config\Services::curlrequest();

    try {
      // Intentar obtener información del usuario actual
      $response = $client->get($this->baseUrl . '/me', [
        'headers' => [
          'Authorization' => 'Bearer ' . $accessToken,
          'Content-Type' => 'application/json'
        ]
      ]);

      $statusCode = $response->getStatusCode();
      $body = json_decode($response->getBody(), true);

      return $this->response->setJSON([
        'success' => $statusCode === 200,
        'message' => $statusCode === 200 ? 'Frame.io API access confirmed' : 'Frame.io API access denied',
        'status_code' => $statusCode,
        'data' => $body
      ]);
    } catch (\Exception $e) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Error testing Frame.io access: ' . $e->getMessage()
      ]);
    }
  }
}
