<?php

namespace App\Tests;

use App\Entity\Image;
use App\Entity\Category;
use App\Entity\Prestation;
use App\Factory\UserFactory;
use App\Factory\ImageFactory;
use App\Factory\CategoryFactory;
use App\Factory\PrestationFactory;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class PrestationResourceTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreatePrestationWithoutAuthentication(): void
    {
        $client = self::createClient();
        $category = CategoryFactory::createOne();
        $image = ImageFactory::createOne(['prestation' => null]);
        $client->request('POST', '/api/prestations', [
            'json' => [
                'title' => 'test',
                'content' => 'test content',
                'price' => 10000,
                'duration' => '2:00',
                'category' => '/api/categories/' . $category->getId(),
                'images' => ['/api/images/' . $image->getId()],
            ],
        ]);
        $this->assertResponseStatusCodeSame(401);
    }
    public function testCreatePrestationNotAllowedUser(): void
    {
        $client = self::createClient();
        $category = CategoryFactory::createOne();
        $image = ImageFactory::createOne(['prestation' => null]);
        $user = UserFactory::createOne();
        Auth::createAuthenticatedClient($client, $user);
        $client->request('POST', '/api/prestations', [
            'json' => [
                'title' => 'test',
                'content' => 'test content',
                'price' => 10000,
                'duration' => '2:00',
                'category' => '/api/categories/' . $category->getId(),
                'images' => ['/api/images/' . $image->getId()],
            ],
        ]);
        $this->assertResponseStatusCodeSame(403);
    }
    public function testCreatePrestationAllowedAdminUser(): void
    {
        $client = self::createClient();
        $category = CategoryFactory::createOne();
        $image = ImageFactory::createOne(['prestation' => null]);
        $admin = UserFactory::new()->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $response = $client->request('POST', '/api/prestations', [
            'json' => [
                'title' => 'test',
                'content' => 'test content',
                'exerpt' => 'test exertp',
                'price' => 10000,
                'duration' => '2:00',
                'category' => '/api/categories/' . $category->getId(),
                'images' => ['/api/images/' . $image->getId()],
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);

        $prestationIri = $this->findIriBy(Prestation::class, ['title' => 'test']);

        $client->request('GET', '/api/categories/' . $category->getId());
        $this->assertJsonContains([
            'prestations' => [
                [
                    '@id' => $prestationIri,
                ]
            ]
        ]);

        $client->request('GET', '/api/images/' . $image->getId());
        $this->assertJsonContains([
            'prestation' => [
                '@id' => $prestationIri,
            ]
        ]);
    }
    public function testCreatePrestationTitleShouldNotBeBlank(): void
    {
        $client = self::createClient();
        $category = CategoryFactory::createOne();
        $image = ImageFactory::createOne(['prestation' => null]);
        $admin = UserFactory::new()->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $client->request('POST', '/api/prestations', [
            'json' => [
                'content' => 'test content',
                'exerpt' => 'test exertp',
                'price' => 10000,
                'duration' => '2:00',
                'category' => '/api/categories/' . $category->getId(),
                'images' => ['/api/images/' . $image->getId()],
            ],
        ]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            "hydra:description" => "title: This value should not be blank."
        ]);
    }
    public function testCreatePrestationContentShouldNotBeBlank(): void
    {
        $client = self::createClient();
        $category = CategoryFactory::createOne();
        $image = ImageFactory::createOne(['prestation' => null]);
        $admin = UserFactory::new()->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $client->request('POST', '/api/prestations', [
            'json' => [
                'title' => 'title',
                'exerpt' => 'test exertp',
                'price' => 10000,
                'duration' => '2:00',
                'category' => '/api/categories/' . $category->getId(),
                'images' => ['/api/images/' . $image->getId()],
            ],
        ]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            "hydra:description" => "content: This value should not be blank."
        ]);
    }
    public function testCreatePrestationCategoryShouldNotBeBlank(): void
    {
        $client = self::createClient();
        $image = ImageFactory::createOne(['prestation' => null]);
        $admin = UserFactory::new()->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $client->request('POST', '/api/prestations', [
            'json' => [
                'title' => 'title',
                'content' => 'content',
                'exerpt' => 'test exertp',
                'price' => 10000,
                'duration' => '2:00',
                'images' => ['/api/images/' . $image->getId()],
            ],
        ]);
        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains([
            "hydra:description" => "category: This value should not be blank."
        ]);
    }

    public function testGetPrestationCollection(): void
    {

        $client = self::createClient();
        PrestationFactory::new(function () {
            return [
                'category' => CategoryFactory::createOne(),
                'images' => ImageFactory::new()->many(1),
            ];
        })->many(3)->create();
        $client->request('GET', '/api/prestations');
        $this->assertResponseStatusCodeSame(200);
        // $this->assertJsonContains(['hydra:totalItems' => 3]);
    }

    public function testGetPrestationItem(): void
    {

        $client = self::createClient();

        $prestation = PrestationFactory::createOne([
            'category' => CategoryFactory::createOne()
        ]);
        $client->request('GET', "/api/prestations/" . $prestation->getId());

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'id' => $prestation->getId(),
            'title' => $prestation->getTitle(),
            'content' => $prestation->getContent(),
            'images' => []
        ]);
    }

    public function testUpdatePrestation(): void
    {

        $client = self::createClient();
        $image = ImageFactory::createOne();
        $category = CategoryFactory::createOne();
        $prestation = PrestationFactory::createOne([
            'category' => CategoryFactory::createOne(),
            'images' => ImageFactory::createMany(1),
        ]);
        $client->request(
            'PUT',
            "/api/prestations/" . $prestation->getId(),
            [
                'json' => [
                    'title' => 'updated title',
                    'content' => 'updated content',
                    'exerpt' => 'updated exertp',
                    'price' => 20000,
                    'duration' => '00:30',
                    'category' => '/api/categories/' . $category->getId(),
                    'images' => ['/api/images/' . $image->getId()]
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(401);

        $user = UserFactory::createOne();
        Auth::createAuthenticatedClient($client, $user);
        $client->request(
            'PUT',
            "/api/prestations/" . $prestation->getId(),
            [
                'json' => [
                    'title' => 'updated title',
                    'content' => 'updated content',
                    'exerpt' => 'updated exertp',
                    'price' => 20000,
                    'duration' => '00:30',
                    'category' => '/api/categories/' . $category->getId(),
                    'images' => ['/api/images/' . $image->getId()]
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(403);

        $admin = UserFactory::new()->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $client->request(
            'PUT',
            "/api/prestations/" . $prestation->getId(),
            [
                'json' => [
                    'title' => 'updated title',
                    'content' => 'updated content',
                    'exerpt' => 'updated exertp',
                    'price' => 20000,
                    'duration' => '00:30',
                    'category' => '/api/categories/' . $category->getId(),
                    'images' => ['/api/images/' . $image->getId()]
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'title' => 'updated title',
            'content' => 'updated content',
            'exerpt' => 'updated exertp',
            'price' => 20000,
            'duration' => '00:30',
            'category' => ['@id' => '/api/categories/' . $category->getId()],
            'images' => [['@id' => '/api/images/' . $image->getId()]]
        ]);
    }

    public function testDeletePrestation(): void
    {

        $client = self::createClient();
        $category = CategoryFactory::createOne();
        $img = ImageFactory::createOne();
        $prestation = PrestationFactory::createOne([
            'category' => $category,
            'images' => [
                $img,
            ],
        ]);

        $client->request(
            'DELETE',
            "/api/prestations/" . $prestation->getId(),
        );
        $this->assertResponseStatusCodeSame(401);


        $user = UserFactory::createOne();
        Auth::createAuthenticatedClient($client, $user);
        $client->request(
            'DELETE',
            "/api/prestations/" . $prestation->getId(),
        );
        $this->assertResponseStatusCodeSame(403);

        $admin = UserFactory::new()->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $client->request(
            'DELETE',
            "/api/prestations/" . $prestation->getId(),
        );

        $this->assertResponseStatusCodeSame(204);

        // $client->request(
        //     'GET',
        //     "/api/categories/" . $category->getId(),
        // );
        // $this->assertResponseIsSuccessful();
        // $this->assertJsonContains([
        //     'prestations' => []
        // ]);
        // $client->request(
        //     'GET',
        //     "/api/images/" . $img->getId(),
        // );
        // $this->assertResponseIsSuccessful();
        // $this->assertJsonContains([
        //     'prestation' => null
        // ]);
    }
}
