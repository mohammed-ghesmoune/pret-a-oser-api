<?php

namespace App\Tests;

use App\Factory\UserFactory;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;


class UserResourceTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateUser(): void
    {
        $client = self::createClient();
        $client->request('POST', '/api/users', [
            'json' => [
                'email' => 'user@example.com',
                'username' => 'user',
                'plainPassword' => '1234'
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'email' => 'user@example.com',
            'username' => 'user',
        ]);
    }


    public function testGetUserCollectionWithoutAuthentication(): void
    {

        $client = self::createClient();
        UserFactory::createMany(3);
        $client->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testGetUserCollectionWithAuthenticatedUser(): void
    {

        $client = self::createClient();
        UserFactory::createMany(3);
        $user1 = UserFactory::createOne();
        Auth::createAuthenticatedClient($client, $user1);
        $client->request('GET', '/api/users');
        $this->assertJsonContains(['hydra:totalItems' => 1]);
    }

    public function testGetUserCollectionWithAuthenticatedAdmin(): void
    {
        $client = self::createClient();
        UserFactory::createMany(3);
        $admin = UserFactory::new()->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $client->request('GET', '/api/users');
        $this->assertJsonContains(['hydra:totalItems' => 4]);
    }

    public function testGetUserItemWithoutAuthentication(): void
    {

        $client = self::createClient();

        $user1 = UserFactory::createOne();

        $client->request('GET', "/api/users/" . $user1->getId());
        $this->assertResponseStatusCodeSame(401);
    }
    public function testGetUserItemNotAuthenticatedUser(): void
    {

        $client = self::createClient();

        $user1 = UserFactory::createOne();
        $user2 = UserFactory::createOne();

        Auth::createAuthenticatedClient($client, $user2);
        $client->request('GET', "/api/users/" . $user1->getId());
        $this->assertResponseStatusCodeSame(403);
    }
    public function testGetUserItemAuthenticatedUser(): void
    {

        $client = self::createClient();

        $user1 = UserFactory::createOne();


        Auth::createAuthenticatedClient($client, $user1);
        $client->request('GET', "/api/users/" . $user1->getId());
        $this->assertResponseStatusCodeSame(200);
    }
    public function testGetUserItemAdmin(): void
    {

        $client = self::createClient();

        $user1 = UserFactory::createOne();
        $admin = UserFactory::new([
            'email' => 'admin@example.com',
            'plainPassword' => '1234',
            'username' => 'admin'
        ])->admin()->create();

        Auth::createAuthenticatedClient($client, $admin);
        $client->request('GET', "/api/users/" . $user1->getId());
        $this->assertResponseStatusCodeSame(200);
    }
    public function testUpdateUserWithoutAuthentication(): void
    {

        $client = self::createClient();
        $user1 = UserFactory::createOne();
        $client->request(
            'PUT',
            "/api/users/" . $user1->getId(),
            [
                'json' => [
                    'username' => 'updated username'
                ]
            ]
        );
        $this->assertResponseStatusCodeSame(401);
    }
    public function testUpdateUserNotAuthenticatedUser(): void
    {

        $client = self::createClient();
        $user1 = UserFactory::createOne();
        $user2 = UserFactory::createOne();
        Auth::createAuthenticatedClient($client, $user1);
        $client->request(
            'PUT',
            "/api/users/" . $user2->getId(),
            [
                'json' => [
                    'username' => 'updated username'
                ]
            ]
        );
        $this->assertResponseStatusCodeSame(403);
    }
    public function testUpdateUserAuthenticatedUser(): void
    {
        $client = self::createClient();
        $user1 = UserFactory::createOne();
        Auth::createAuthenticatedClient($client, $user1);
        $client->request(
            'PUT',
            "/api/users/" . $user1->getId(),
            [
                'json' => [
                    'username' => 'updated username'
                ]
            ]
        );
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'email' => $user1->getEmail(),
            'username' => 'updated username'
        ]);
    }
    public function testUpdateUserAuthenticatedAdmin(): void
    {
        $client = self::createClient();
        $user1 = UserFactory::createOne();
        $admin = UserFactory::new([
            'email' => 'admin@example.com',
            'plainPassword' => '1234',
            'username' => 'admin'
        ])->admin()->create();

        Auth::createAuthenticatedClient($client, $admin);
        $client->request(
            'PUT',
            "/api/users/" . $user1->getId(),
            [
                'json' => [
                    'username' => 'updated username'
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'email' => $user1->getEmail(),
            'username' => 'updated username'
        ]);
    }

    public function testDeleteUserWithoutAuthentication(): void
    {

        $client = self::createClient();
        $user1 = UserFactory::createOne();
        $client->request(
            'DELETE',
            "/api/users/" . $user1->getId(),
        );
        $this->assertResponseStatusCodeSame(401);
    }
    public function testDeleteUserNotAuthenticatedUser(): void
    {

        $client = self::createClient();
        $user1 = UserFactory::createOne();
        $user2 = UserFactory::createOne();
        Auth::createAuthenticatedClient($client, $user1);
        $client->request(
            'DELETE',
            "/api/users/" . $user2->getId(),
        );
        $this->assertResponseStatusCodeSame(403);
    }
    public function testDeleteUserAuthenticatedUser(): void
    {
        $client = self::createClient();
        $user1 = UserFactory::createOne();
        Auth::createAuthenticatedClient($client, $user1);
        $client->request(
            'DELETE',
            "/api/users/" . $user1->getId(),
        );
        $this->assertResponseStatusCodeSame(403, 'Only Admin can delete user');
    }
    public function testDeleteUserAuthenticatedAdmin(): void
    {
        $client = self::createClient();
        $user1 = UserFactory::createOne();
        $admin = UserFactory::new([
            'email' => 'admin@example.com',
            'plainPassword' => '1234',
            'username' => 'admin'
        ])->admin()->create();

        Auth::createAuthenticatedClient($client, $admin);
        $client->request(
            'DELETE',
            "/api/users/" . $user1->getId(),
        );

        $this->assertResponseStatusCodeSame(204);
    }
}
