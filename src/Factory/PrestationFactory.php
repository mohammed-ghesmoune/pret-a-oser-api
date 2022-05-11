<?php

namespace App\Factory;

use App\Entity\Prestation;
use App\Repository\PrestationRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Prestation>
 *
 * @method static Prestation|Proxy createOne(array $attributes = [])
 * @method static Prestation[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Prestation|Proxy find(object|array|mixed $criteria)
 * @method static Prestation|Proxy findOrCreate(array $attributes)
 * @method static Prestation|Proxy first(string $sortedField = 'id')
 * @method static Prestation|Proxy last(string $sortedField = 'id')
 * @method static Prestation|Proxy random(array $attributes = [])
 * @method static Prestation|Proxy randomOrCreate(array $attributes = [])
 * @method static Prestation[]|Proxy[] all()
 * @method static Prestation[]|Proxy[] findBy(array $attributes)
 * @method static Prestation[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Prestation[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static PrestationRepository|RepositoryProxy repository()
 * @method Prestation|Proxy create(array|callable $attributes = [])
 */
final class PrestationFactory extends ModelFactory
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
            'title' => self::faker()->words(3, true),
            'exerpt' => self::faker()->text(80),
            'content' => self::faker()->text(),
            'price' => self::faker()->numberBetween(5000, 30000),
            'duration' => (string) self::faker()->numberBetween(1, 5) . ":00",
            'category' => CategoryFactory::new(),

        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Prestation $prestation): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Prestation::class;
    }
}
