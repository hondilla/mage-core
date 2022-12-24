<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction\Contracts;

use Omatech\MageCore\Editora\Domain\Extraction\Results;

interface ExtractionInterface
{
    public function instancesBy(array $params): Results;
    public function findRelatedInstances(string $instanceUuid, array $params): Results;
}
