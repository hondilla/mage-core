<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance\Validator\Contracts;

use Omatech\MageCore\Editora\Domain\Value\BaseValue;

interface UniqueValueInterface
{
    public function isUnique(BaseValue $value): bool;
}
