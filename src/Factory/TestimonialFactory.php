<?php

namespace App\Factory;

use App\Entity\Testimonial;
use App\Repository\TestimonialRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Testimonial>
 *
 * @method static Testimonial|Proxy createOne(array $attributes = [])
 * @method static Testimonial[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Testimonial|Proxy find(object|array|mixed $criteria)
 * @method static Testimonial|Proxy findOrCreate(array $attributes)
 * @method static Testimonial|Proxy first(string $sortedField = 'id')
 * @method static Testimonial|Proxy last(string $sortedField = 'id')
 * @method static Testimonial|Proxy random(array $attributes = [])
 * @method static Testimonial|Proxy randomOrCreate(array $attributes = [])
 * @method static Testimonial[]|Proxy[] all()
 * @method static Testimonial[]|Proxy[] findBy(array $attributes)
 * @method static Testimonial[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Testimonial[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static TestimonialRepository|RepositoryProxy repository()
 * @method Testimonial|Proxy create(array|callable $attributes = [])
 */
final class TestimonialFactory extends ModelFactory
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
            'content' => self::faker()->text(),
            'author' => self::faker()->name(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Testimonial $testimonial): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Testimonial::class;
    }
}
