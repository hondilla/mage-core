<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Clazz;

use function Lambdish\Phunctional\flat_map;
use function Lambdish\Phunctional\map;

final class RelationCollection
{
    private array $relations;

    public function __construct(array $relations)
    {
        $this->relations = flat_map(
            static fn (array $classes, $key): Relation => new Relation($key, $classes),
            $relations
        );
    }

    public function get(): array
    {
        return $this->relations;
    }

    public function toArray(): array
    {
        return map(static fn (Relation $relation) => $relation->toArray(), $this->relations);
    }
}
