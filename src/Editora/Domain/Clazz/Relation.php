<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Clazz;

use Omatech\MageCore\Editora\Domain\Clazz\Exceptions\InvalidRelationClassException;

final class Relation
{
    public function __construct(private string $key, private array $classes)
    {
    }

    public function validate(array $classes): void
    {
        $diff = array_diff($classes, $this->classes);
        if ($diff !== []) {
            InvalidRelationClassException::withRelationClasses($this->key, $diff);
        }
    }

    public function key(): string
    {
        return $this->key;
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'classes' => $this->classes,
        ];
    }
}
