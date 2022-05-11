<?php

namespace App\DataFixtures;

use App\Factory\LogoFactory;
use App\Factory\PageFactory;
use App\Factory\UserFactory;
use App\Factory\ImageFactory;
use App\Factory\CategoryFactory;
use App\Factory\PrestationFactory;
use App\Factory\TestimonialFactory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $admin = UserFactory::new([
            'email' => 'admin@example.com',
            'username' => 'admin',
        ])->admin()->create();

        $user = UserFactory::new()->create([
            'email' => 'user@example.com',
            'username' => 'user',
        ]);
        UserFactory::new()->createMany(3);

        TestimonialFactory::createMany(7);
        LogoFactory::createMany(6);

        $pageTitles = ['home', 'about', 'contact'];
        PageFactory::createMany(3, function () use ($pageTitles) {
            static $i = 0;
            $pageTitle = $pageTitles[$i];
            $i++;
            return [
                'title' => $pageTitle
            ];
        });

        $categoryTitles = ['service', 'forfait', 'soirée fille', 'bon cadeau'];

        CategoryFactory::createMany(4, function () use ($categoryTitles) {
            static $i = 0;
            $categoryTitle = $categoryTitles[$i];
            $i++;
            return [
                'title' => $categoryTitle
            ];
        });

        foreach ($categoryTitles as $category) {

            PrestationFactory::new(function () use ($category) {
                return [
                    'category' => CategoryFactory::find(['title' => $category]),
                    'images' => ImageFactory::new()->many(1),
                ];
            })->many(1, 7)->create();
        }
    }
}
