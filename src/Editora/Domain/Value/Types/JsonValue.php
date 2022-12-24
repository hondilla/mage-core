<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Value\Types;

use Omatech\MageCore\Editora\Domain\Value\BaseValue;

final class JsonValue extends BaseValue
{
    public function value(): ?string
    {
        return $this->value;
    }
}
