<?php

namespace App\Libraries;

use GuzzleHttp\Client;

class FrameIoService
{
  protected $client;
  protected $clientId;
  protected $clientSecret;
  protected $redirectUri;

  public function __construct()
  {
    $this->clientId     = env('FRAMEIO_CLIENT_ID');
    $this->clientSecret = env('FRAMEIO_CLIENT_SECRET');
    $this->redirectUri  = env('FRAMEIO_REDIRECT_URI');

    $this->client = new Client([
      'base_uri' => 'https://api.frame.io/v4/',
      'timeout'  => 5.0,
    ]);
  }

  /**
   * Genera la URL para que el usuario haga login en Adobe
   */
  public function getAuthUrl()
  {
    $baseUrl = "https://ims-na1.adobelogin.com/ims/authorize/v2";
    $params = [
      'client_id'     => $this->clientId,
      'redirect_uri'  => $this->redirectUri,
      'scope'         => 'openid,AdobeID,frameio_api,offline_access',
      'response_type' => 'code'
    ];

    return $baseUrl . '?' . http_build_query($params);
  }
}
