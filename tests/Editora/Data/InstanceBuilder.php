<?php

namespace Tests\Editora\Data;

use Omatech\MageCore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Omatech\MageCore\Editora\Domain\Instance\Instance;
use Omatech\MageCore\Editora\Domain\Instance\InstanceBuilder as InstanceBuilderReal;

class InstanceBuilder
{
    private array $languages = ['es', 'en'];
    private string $className = 'VideoGames';
    private array $structure;
    private InstanceCacheInterface $instanceCache;

    public function __construct()
    {
        $this->structure = (include dirname(__DIR__).'/Data/structure.php')['classes'];
    }

    public function build(): Instance
    {
        return (new InstanceBuilderReal($this->instanceCache))
            ->setLanguages($this->languages)
            ->setClassName($this->className)
            ->setStructure($this->structure[$this->className] ?? [])
            ->build();
    }

    public function setLanguages(array $languages): InstanceBuilder
    {
        $this->languages = array_fill_keys($languages, []);
        return $this;
    }

    public function setStructure(array $structure): InstanceBuilder
    {
        $this->structure = [$this->className => $structure];
        return $this;
    }

    public function setClassName(string $className): InstanceBuilder
    {
        $this->className = $className;
        return $this;
    }

    public function setInstanceCache(InstanceCacheInterface $instanceCache): InstanceBuilder
    {
        $this->instanceCache = $instanceCache;
        return $this;
    }
}