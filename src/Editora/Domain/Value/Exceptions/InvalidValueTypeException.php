<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Value\Exceptions;

use Exception;

final class InvalidValueTypeException extends Exception
{
    public static function withType(string $type): never
    {
        throw new self("{$type} do not exists.");
    }
}
