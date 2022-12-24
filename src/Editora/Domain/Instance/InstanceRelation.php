<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance;

final readonly class InstanceRelation
{
    public function __construct(private string $key, private array $instances)
    {
    }

    public function key(): string
    {
        return $this->key;
    }

    public function instances(): array
    {
        return $this->instances;
    }

    public function instanceExists(string $uuid): bool
    {
        return isset($this->instances[$uuid]);
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'instances' => $this->instances,
        ];
    }
}
