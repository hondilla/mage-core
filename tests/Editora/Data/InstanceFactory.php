<?php

namespace Tests\Editora\Data;

use Mockery;
use Omatech\MageCore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Omatech\MageCore\Editora\Domain\Instance\Instance;

class InstanceFactory
{
    public static function get(string $className): Instance
    {
        return (new InstanceBuilder())
            ->setInstanceCache(self::mock())
            ->setClassName($className)
            ->build();
    }

    public static function fill(string $className, callable $fn): Instance
    {
        $instance = (new InstanceBuilder())
            ->setInstanceCache(self::mock())
            ->setClassName($className)
            ->build();
        return $instance->fill($fn(new InstanceArrayBuilder()));
    }

    private static function mock(): InstanceCacheInterface
    {
        $instanceCache = Mockery::mock(InstanceCacheInterface::class);
        $instanceCache->shouldReceive('get')->andReturn(null)->once();
        $instanceCache->shouldReceive('put')->andReturn(null)->once();
        return $instanceCache;
    }
}