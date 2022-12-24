<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance\Contracts;

use Omatech\MageCore\Editora\Domain\Instance\Instance;

interface InstanceCacheInterface
{
    public function get(string $className): ?Instance;
    public function put(string $className, Instance $instance): void;
}
