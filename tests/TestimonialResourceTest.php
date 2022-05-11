<?php

namespace App\Tests;

use App\Factory\UserFactory;
use App\Factory\TestimonialFactory;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;


class TestimonialResourceTest extends ApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateTestimonialWithoutAuthentication(): void
    {
        $client = self::createClient();
        $client->request('POST', '/api/testimonials', [
            'json' => [
                'title' => 'test',
                'content' => 'test content',
                'author' => 'author'
            ],
        ]);
        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateTestimonialRoleUser(): void
    {
        $client = self::createClient();
        $user = UserFactory::createOne();
        Auth::createAuthenticatedClient($client, $user);
        $client->request('POST', '/api/testimonials', [
            'json' => [
                'title' => 'test',
                'content' => 'test content',
                'author' => 'author'
            ],
        ]);
        $this->assertResponseStatusCodeSame(403);
    }
    public function testCreateTestimonialRoleAdmin(): void
    {
        $client = self::createClient();
        $admin = UserFactory::new()->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $client->request('POST', '/api/testimonials', [
            'json' => [
                'title' => 'test',
                'content' => 'test content',
                'author' => 'author'
            ],
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'title' => 'test',
            'content' => 'test content',
            'author' => 'author'
        ]);
    }

    public function testGetTestimonialCollection(): void
    {

        $client = self::createClient();
        TestimonialFactory::createMany(3);
        $client->request('GET', '/api/testimonials');
        $this->assertJsonContains(['hydra:totalItems' => 3]);
    }

    public function testGetTestimonialItem(): void
    {

        $client = self::createClient();

        $testimonial = TestimonialFactory::createOne();
        $client->request('GET', "/api/testimonials/" . $testimonial->getId());

        $this->assertResponseStatusCodeSame(200);

        $this->assertJsonContains([
            'id' => $testimonial->getId(),
            'title' => $testimonial->getTitle(),
            'content' => $testimonial->getContent(),
            'author' => $testimonial->getAuthor(),
        ]);
    }
    public function testUpdateTestimonial(): void
    {

        $client = self::createClient();
        $testimonial = TestimonialFactory::createOne();
        $client->request(
            'PUT',
            "/api/testimonials/" . $testimonial->getId(),
            [
                'json' => [
                    'title' => 'updated title'
                ]
            ]
        );

        $this->assertResponseStatusCodeSame(401);

        $admin = UserFactory::new()->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $client->request(
            'PUT',
            "/api/testimonials/" . $testimonial->getId(),
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

    public function testDeleteTestimonial(): void
    {

        $client = self::createClient();
        $testimonial = TestimonialFactory::createOne();
        $client->request(
            'DELETE',
            "/api/testimonials/" . $testimonial->getId(),
        );

        $this->assertResponseStatusCodeSame(401);

        $admin = UserFactory::new()->admin()->create();
        Auth::createAuthenticatedClient($client, $admin);
        $client->request(
            'DELETE',
            "/api/testimonials/" . $testimonial->getId(),
        );

        $this->assertResponseStatusCodeSame(204);
    }
}
