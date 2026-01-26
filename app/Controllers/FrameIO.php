<?php

namespace App\Controllers;

use App\Libraries\FrameIoService;

class FrameIO extends BaseController
{
  private $clientId;
  private $clientSecret;
  private $redirectUri;
  private $authUrl;
  private $tokenUrl;

  public function __construct()
  {
    $this->clientId = env('FRAMEIO_CLIENT_ID');
    $this->clientSecret = env('FRAMEIO_CLIENT_SECRET');
    $this->redirectUri = env('FRAMEIO_REDIRECT_URI');
    $this->authUrl = env('FRAMEIO_AUTH_URL');
    $this->tokenUrl = env('FRAMEIO_TOKEN_URL');
  }

  public function index()
  {
    echo "frame io";
  }

  public function login()
  {
    $state = bin2hex(random_bytes(16));
    session()->set('oauth_state', $state);

    $params = [
      'client_id' => $this->clientId,
      'redirect_uri' => $this->redirectUri,
      'scope' => 'openid,additional_info.roles,offline_access,email,profile',
      'response_type' => 'code',
      'state' => $state
    ];

    $authUrl = $this->authUrl . '?' . http_build_query($params);

    return redirect()->to($authUrl);
  }

  public function callback()
  {
    $request = service('request');

    $state = $request->getGet('state');
    if ($state !== session()->get('oauth_state')) {
      return redirect()->to('/')->with('error', 'Estado OAuth inválido');
    }

    $code = $request->getGet('code');
    if (!$code) {
      return redirect()->to('/')->with('error', 'Código de autorización no recibido');
    }

    $tokenData = $this->getAccessToken($code);

    if ($tokenData) {
      session()->set([
        'access_token' => $tokenData['access_token'],
        'refresh_token' => $tokenData['refresh_token'] ?? null,
        'expires_in' => $tokenData['expires_in'] ?? 3600
      ]);

      echo "<pre>";
      print_r($tokenData);
      echo "</pre>";
    }

    echo "Error al obtener token de acceso";
  }

  public function logout()
  {
    session()->destroy();
    return redirect()->to('/')->with('success', 'Sesión cerrada correctamente');
  }

  private function getAccessToken($code)
  {
    $data = [
      'client_id' => $this->clientId,
      'client_secret' => $this->clientSecret,
      'code' => $code,
      'grant_type' => 'authorization_code',
      'redirect_uri' => $this->redirectUri
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $this->tokenUrl,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => http_build_query($data),
      CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded'
      ]
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpCode === 200) {
      return json_decode($response, true);
    }

    log_message('error', 'Error getting access token: ' . $response);
    return false;
  }
}
