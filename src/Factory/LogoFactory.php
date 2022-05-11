<?php

namespace App\Factory;

use App\Entity\Logo;
use App\Repository\LogoRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Logo>
 *
 * @method static Logo|Proxy createOne(array $attributes = [])
 * @method static Logo[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Logo|Proxy find(object|array|mixed $criteria)
 * @method static Logo|Proxy findOrCreate(array $attributes)
 * @method static Logo|Proxy first(string $sortedField = 'id')
 * @method static Logo|Proxy last(string $sortedField = 'id')
 * @method static Logo|Proxy random(array $attributes = [])
 * @method static Logo|Proxy randomOrCreate(array $attributes = [])
 * @method static Logo[]|Proxy[] all()
 * @method static Logo[]|Proxy[] findBy(array $attributes)
 * @method static Logo[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Logo[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static LogoRepository|RepositoryProxy repository()
 * @method Logo|Proxy create(array|callable $attributes = [])
 */
final class LogoFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'title' => self::faker()->words(2, true),
            'url' => 'https://google.com',
            'imageName' => self::faker()->imageUrl(300, 300),

        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Logo $logo): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Logo::class;
    }
}
