<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Auth extends Controller
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

  public function login()
  {
    // Generar state aleatorio para seguridad
    $state = bin2hex(random_bytes(16));
    session()->set('oauth_state', $state);

    // Parámetros para la autorización OAuth2
    $params = [
      'client_id' => $this->clientId,
      'redirect_uri' => $this->redirectUri,
      'scope' => 'additional_info.roles,email,offline_access,openid,profile',
      'response_type' => 'code',
      'state' => $state
    ];

    $authUrl = $this->authUrl . '?' . http_build_query($params);



    return redirect()->to($authUrl);


    //

    // Construir URL de autorización de Adobe IMS
    // $authUrl = 'https://ims-na1.adobelogin.com/ims/authorize/v1?' . http_build_query([
    //   'client_id' => $this->clientId,
    //   'redirect_uri' => $this->redirectUri,
    //   'response_type' => 'code',
    //   'scope' => 'additional_info.roles,email,offline_access,openid,profile',
    //   'state' => $state
    // ]);

    // return redirect()->to($authUrl);
  }

  public function callback()
  {
    $request = service('request');

    // Verificar state para prevenir CSRF
    $state = $request->getGet('state');
    if ($state !== session()->get('oauth_state')) {
      return redirect()->to('/')->with('error', 'Estado OAuth inválido');
    }

    $code = $request->getGet('code');
    if (!$code) {
      return redirect()->to('/')->with('error', 'Código de autorización no recibido');
    }

    // Intercambiar código por token de acceso
    $tokenData = $this->getAccessToken($code);

    if ($tokenData) {
      // Guardar tokens en sesión
      session()->set([
        'access_token' => $tokenData['access_token'],
        'refresh_token' => $tokenData['refresh_token'] ?? null,
        'expires_in' => $tokenData['expires_in'] ?? 3600
      ]);

      return redirect()->to('/dashboard')->with('success', 'Autenticación exitosa');
    }

    return redirect()->to('/')->with('error', 'Error al obtener token de acceso');
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

  public function logout()
  {
    session()->destroy();
    return redirect()->to('/')->with('success', 'Sesión cerrada correctamente');
  }
}
