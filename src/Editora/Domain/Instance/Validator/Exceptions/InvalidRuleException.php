<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance\Validator\Exceptions;

use Exception;

final class InvalidRuleException extends Exception
{
    public static function withRule(string $rule): never
    {
        throw new self("The rule {$rule} do not exists.");
    }
}
