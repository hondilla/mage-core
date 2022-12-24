<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance\Validator\Rules;

use Omatech\MageCore\Editora\Domain\Instance\Validator\Exceptions\RequiredValueException;
use Omatech\MageCore\Editora\Domain\Value\BaseValue;
use Omatech\MageCore\Shared\Utils\Utils;

final class Required extends BaseRule
{
    public function validate(BaseValue $value): void
    {
        if ($this->conditions !== true) {
            return;
        }
        if (! Utils::isEmpty($value->value())) {
            return;
        }
        RequiredValueException::withValue($value);
    }
}
