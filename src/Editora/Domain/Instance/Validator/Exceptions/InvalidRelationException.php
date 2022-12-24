<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance\Validator\Exceptions;

use Exception;

final class InvalidRelationException extends Exception
{
    public static function withRelation(string $key): never
    {
        throw new self("Relation {$key} is not valid.");
    }
}
