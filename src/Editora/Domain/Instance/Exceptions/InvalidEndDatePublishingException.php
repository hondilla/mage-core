<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance\Exceptions;

use Exception;

final class InvalidEndDatePublishingException extends Exception
{
    public static function withDate(string $endDate, string $startDate): never
    {
        throw new self("End publication date ({$endDate}) is before
        the initial publication date ({$startDate}).");
    }
}
