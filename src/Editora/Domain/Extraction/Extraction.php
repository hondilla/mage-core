<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;

final readonly class Extraction
{
    public function __construct(private string $query, private array $results)
    {
    }

    public function query(): string
    {
        return $this->query;
    }

    public function results(): array
    {
        return $this->results;
    }

    public function toArray(): array
    {
        return reduce(static function (array $acc, Query $query): array {
            if (count($query->results()) > 0) {
                $acc[$query->results()[0]->class()] = map(
                    static fn (Instance $instance): array => $instance->toArray(),
                    $query->results()
                );
            }
            return $acc;
        }, $this->results, []);
    }
}
