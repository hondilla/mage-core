<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Value;

use function Lambdish\Phunctional\get_in;

final class Configuration
{
    public function __construct(private array $configuration)
    {
    }

    public function exists(mixed $value, array $path): bool
    {
        return in_array($value, get_in($path, $this->configuration, []), true);
    }

    public function get(): array
    {
        return $this->configuration;
    }
}
