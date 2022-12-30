<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Attribute;

use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\search;

final class AttributeCollection
{
    private array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = map(static fn (Attribute $attribute) => $attribute, $attributes);
    }

    public function fill(array $attributes): void
    {
        each(function (array $values, string $key): void {
            $this->find($key)?->fill($values);
        }, $attributes);
    }

    private function find(string $key): ?Attribute
    {
        return search(static fn (Attribute $attribute): bool => $attribute->key() === $key, $this->attributes);
    }

    public function findAll(string $key): array
    {
        return $this->search($this->attributes, $key);
    }

    private function search(array $attributes, string $key): array
    {
        return reduce(function (array $acc, Attribute $attribute) use ($key) {
            $sub = $this->search($attribute->attributes()->get(), $key);
            if ($attribute->key() === $key) {
                $acc[] = $attribute;
            }
            return array_merge($acc, $sub);
        }, $attributes, []);
    }

    public function get(): array
    {
        return $this->attributes;
    }

    public function toArray(): array
    {
        return map(static fn (Attribute $attribute) => $attribute->toArray(), $this->attributes);
    }
}
