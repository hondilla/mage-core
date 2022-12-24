<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Value\Types;

use Omatech\MageCore\Editora\Domain\Value\Exceptions\LookupValueOptionException;
use Omatech\MageCore\Shared\Utils\Utils;

final class LookupValue extends StringValue
{
    public function fill(array $value): void
    {
        $this->ensureLookupIsValid($value['value']);
        parent::fill($value);
    }

    private function ensureLookupIsValid(mixed $value): void
    {
        if (Utils::isEmpty($value)) {
            return;
        }
        if ($this->configuration->exists($value, ['options'])) {
            return;
        }
        LookupValueOptionException::withValue($this);
    }
}
