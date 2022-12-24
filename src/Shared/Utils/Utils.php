<?php declare(strict_types=1);

namespace Omatech\MageCore\Shared\Utils;

use function Lambdish\Phunctional\filter;

final class Utils
{
    public static function slug(?string $string): ?string
    {
        if (self::isEmpty($string)) {
            return $string;
        }
        return strtolower(trim(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '-$0', $string), '-'));
    }

    public static function isEmpty(mixed $value): bool
    {
        return (bool) filter(static fn ($operator) => $value === $operator, ['', [], null]);
    }
}
