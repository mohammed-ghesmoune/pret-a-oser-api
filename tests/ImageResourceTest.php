<?php

namespace App\Tests;

use App\Factory\UserFactory;
use App\Factory\ImageFactory;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class ImageResourceTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateImageWithoutAuthentication(): void
    {
        $image = new UploadedFile(__DIR__ . '/images/image1.png', 'image1.png');

        $client = self::createClient();
        $client->request('POST', '/api/images', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                    'title' => 'Image Title',
                ],
                'files' => [
                    'imageFile' => $image,
                ],
            ]
        ]);
        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateImageWithNotAllowedUser(): void
    {
        $image = new UploadedFile(__DIR__ . '/images/image1.png', 'image1.png');

        $client = self::createClient();
        $user = UserFactory::createOne();
        Auth::createAuthenticatedClient($client, $user);
        $client->request('POST', '/api/images', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                    'title' => 'Image Title',
                ],
                'files' => [
                    'imageFile' => $image,
                ],
            ]
        ]);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testCreateImageWithAllowedAdminUser(): void
    {
        $image = new UploadedFile(__DIR__ . '/images/image1.png', 'image1.png');

        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $client->request('POST', '/api/images', [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                    'title' => 'Image Title',
                ],
                'files' => [
                    'imageFile' => $image,
                ],
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->assertJsonContains([
            'title' => 'Image Title',
        ]);
    }

    public function testGetImageCollection(): void
    {
        $client = self::createClient();
        ImageFactory::createMany(3, [
            'prestation' => null,
        ]);
        $client->request('GET', '/api/images');
        $this->assertJsonContains(['hydra:totalItems' => 3]);
    }

    public function testGetImageItem(): void
    {

        $client = self::createClient();

        $image = ImageFactory::createOne(['prestation' => null]);
        $client->request('GET', "/api/images/" . $image->getId());

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'id' => $image->getId(),
            'title' => $image->getTitle(),
        ]);
    }

    public function testUpdateImage(): void
    {

        $image2 = new UploadedFile(__DIR__ . '/images/image2.png', 'image2.png');
        $client = self::createClient();
        $image1 = ImageFactory::createOne(['prestation' => null]);

        $user = UserFactory::createOne();
        Auth::createAuthenticatedClient($client, $user);
        $client->request('PUT', '/api/images/' . $image1->getId(), [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                    'title' => 'updated Image Title',
                ],
                'files' => [
                    'imageFile' => $image2,
                ],
            ]
        ]);

        $this->assertResponseStatusCodeSame(405);

        $admin = UserFactory::new()->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $response = $client->request('POST', '/api/images/' . $image1->getId(), [
            'headers' => ['Content-Type' => 'multipart/form-data'],
            'extra' => [
                'parameters' => [
                    'title' => 'updated Image Title',
                ],
                'files' => [
                    'imageFile' => $image2,
                ],
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->assertJsonContains([
            'title' => 'updated Image Title'
        ]);
    }


    public function testDeleteImage(): void
    {

        $client = self::createClient();
        $image = ImageFactory::createOne(['prestation' => null]);

        $client->request('DELETE', "/api/images/" . $image->getId());
        $this->assertResponseStatusCodeSame(401);

        $user = UserFactory::createOne();
        Auth::createAuthenticatedClient($client, $user);
        $client->request('DELETE', "/api/images/" . $image->getId());
        $this->assertResponseStatusCodeSame(403);

        $admin = UserFactory::new()->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $client->request('DELETE', "/api/images/" . $image->getId());
        $this->assertResponseStatusCodeSame(204);
    }
}
