<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Value\Exceptions;

use Exception;
use Omatech\MageCore\Editora\Domain\Value\BaseValue;

final class LookupValueOptionException extends Exception
{
    public static function withValue(BaseValue $value): never
    {
        throw new self("Lookup option value does not
        exist for the attribute {$value->key()} in language {$value->language()}.");
    }
}
