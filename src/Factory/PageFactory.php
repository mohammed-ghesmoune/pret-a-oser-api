<?php

namespace App\Factory;

use App\Entity\Page;
use App\Repository\PageRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Page>
 *
 * @method static Page|Proxy createOne(array $attributes = [])
 * @method static Page[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Page|Proxy find(object|array|mixed $criteria)
 * @method static Page|Proxy findOrCreate(array $attributes)
 * @method static Page|Proxy first(string $sortedField = 'id')
 * @method static Page|Proxy last(string $sortedField = 'id')
 * @method static Page|Proxy random(array $attributes = [])
 * @method static Page|Proxy randomOrCreate(array $attributes = [])
 * @method static Page[]|Proxy[] all()
 * @method static Page[]|Proxy[] findBy(array $attributes)
 * @method static Page[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Page[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PageRepository|RepositoryProxy repository()
 * @method Page|Proxy create(array|callable $attributes = [])
 */
final class PageFactory extends ModelFactory
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
            'content' => self::faker()->text(),
            'imageName' => self::faker()->imageUrl(),

        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Page $page): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Page::class;
    }
}
