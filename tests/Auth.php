<?php

namespace App\Tests;

use App\Entity\User;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;

class Auth
{

  public static function createAuthenticatedClient(Client $client, $user)
  {

    $response = $client->request('POST', '/api/authentication_token', [
      'json' => [
        'email' => $user->getEmail(),
        'password' => $user->getPlainPassword(),

      ]
    ]);
    $token = \json_decode($response->getContent(), true)['token'];
    $client->setDefaultOptions([
      'auth_bearer' => $token,
    ]);
  }
}
