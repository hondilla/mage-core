<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance\Validator\Exceptions;

use Exception;
use Omatech\MageCore\Editora\Domain\Value\BaseValue;

final class UniqueValueException extends Exception
{
    public static function withValue(BaseValue $value): never
    {
        throw new self("The value must be unique for the attribute {$value->key()}
            in language {$value->language()}.");
    }
}
