<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

use function Lambdish\Phunctional\map;

class QueryAttribute
{
    public function __construct(
        protected readonly string $key,
        protected readonly array $attributes
    ) {
    }

    public function key(): string
    {
        return $this->key;
    }

    public function attributes(): array
    {
        return $this->attributes;
    }

    public function toQuery(): array
    {
        return [
            'key' => $this->key,
            'attributes' => map(
                static fn (QueryAttribute $attribute): array => $attribute->toQuery(),
                $this->attributes
            ),
        ];
    }
}
