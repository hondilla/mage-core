<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Clazz;

final class Clazz
{
    private string $key;
    private RelationCollection $relationCollection;

    public function __construct(array $class)
    {
        $this->key = $class['key'];
        $this->relationCollection = new RelationCollection($class['relations']);
    }

    public function relations(): RelationCollection
    {
        return $this->relationCollection;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'relations' => $this->relationCollection->toArray(),
        ];
    }
}
