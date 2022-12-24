<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

final class Relation
{
    private array $instances = [];

    public function __construct(private readonly string $key, private readonly string $type)
    {
    }

    public function key(): string
    {
        return $this->key;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function instances(): array
    {
        return $this->instances;
    }

    public function setInstances(array $instances): Relation
    {
        $this->instances = $instances;
        return $this;
    }
}
