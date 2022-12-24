<?php declare(strict_types=1);

namespace Tests\Editora\Data;

use Omatech\MageCore\Editora\Domain\Instance\Validator\Contracts\UniqueValueInterface;
use Omatech\MageCore\Editora\Domain\Value\BaseValue;

final class UniqueValueRepository implements UniqueValueInterface
{
    public function isUnique(BaseValue $value): bool
    {
        return !($value->uuid() === 'fake-uuid' && $value->key() === 'sub-title');
    }
}
