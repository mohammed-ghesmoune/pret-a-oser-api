<?php

namespace App\Tests;

use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\CategoryFactory;
use App\Factory\UserFactory;

class CategoryResourceTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateCategoryWithoutAuthentication(): void
    {
        $client = self::createClient();
        $client->request('POST', '/api/categories', [
            'json' => [
                'title' => 'test',
                'descriprtion' => 'test description'
            ],
        ]);
        $this->assertResponseStatusCodeSame(401);
    }
    public function testCreateCategoryRoleUser(): void
    {
        $client = self::createClient();
        $user = UserFactory::createOne([
            'email' => 'user@example.com',
            'plainPassword' => '1234',
            'username' => 'user'
        ]);
        Auth::createAuthenticatedClient($client, $user);
        $client->request('POST', '/api/categories', [
            'json' => [
                'title' => 'test',
                'descriprtion' => 'test description'
            ],
        ]);
        $this->assertResponseStatusCodeSame(403);
    }
    public function testCreateCategoryRoleAdmin(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new([
            'email' => 'admin@example.com',
            'plainPassword' => '1234',
            'username' => 'admin'
        ])->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $response = $client->request('POST', '/api/categories', [
            'json' => [
                'title' => 'test',
                'descriprtion' => 'test description'
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'title' => 'test',
            'slug' => 'test'
        ]);
    }

    public function testGetCategoryCollection(): void
    {

        $client = self::createClient();
        CategoryFactory::createMany(3);
        $client->request('GET', '/api/categories');
        $this->assertJsonContains(['hydra:totalItems' => 3]);
    }

    public function testGetCategoryItem(): void
    {

        $client = self::createClient();

        $category = CategoryFactory::createOne();
        $client->request('GET', "/api/categories/" . $category->getId());

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'id' => $category->getId(),
            'title' => $category->getTitle(),
            'description' => $category->getDescription(),
        ]);
    }
    public function testUpdateCategory(): void
    {

        $client = self::createClient();
        $category = CategoryFactory::createOne();
        $client->request(
            'PUT',
            "/api/categories/" . $category->getId(),
            [
                'json' => [
                    'title' => 'updated title'
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(401);

        $admin = UserFactory::new([
            'email' => 'admin@example.com',
            'plainPassword' => '1234',
            'username' => 'admin'
        ])->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $client->request(
            'PUT',
            "/api/categories/" . $category->getId(),
            [
                'json' => [
                    'title' => 'updated title'
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'title' => 'updated title'
        ]);
    }

    public function testDeleteCategory(): void
    {

        $client = self::createClient();
        $category = CategoryFactory::createOne();
        $client->request(
            'DELETE',
            "/api/categories/" . $category->getId(),
        );

        $this->assertResponseStatusCodeSame(401);

        $admin = UserFactory::new([
            'email' => 'admin@example.com',
            'plainPassword' => '1234',
            'username' => 'admin'
        ])->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $client->request(
            'DELETE',
            "/api/categories/" . $category->getId(),
        );

        $this->assertResponseStatusCodeSame(204);
    }
}
