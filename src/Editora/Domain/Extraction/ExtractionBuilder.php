<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

use Omatech\MageCore\Editora\Domain\Extraction\Contracts\ExtractionInterface;
use Omatech\MageCore\Editora\Domain\Extraction\Instance as ExtractionInstance;
use Omatech\MageCore\Editora\Domain\Instance\Instance;
use function Lambdish\Phunctional\flat_map;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;

final class ExtractionBuilder
{
    private string $query;

    public function __construct(private readonly ExtractionInterface $extraction)
    {
    }

    public function setQuery(string $query): self
    {
        $this->query = $query;
        return $this;
    }

    public function build(): Extraction
    {
        $queryResults = map(function (Query $query): Query {
            $results = $this->extraction->instancesBy($query->params());
            return $query
                ->setPagination($results->pagination())
                ->setResults($this->extractResults($query, $results->instances()));
        }, (new Parser())->parse($this->query));
        return new Extraction($this->query, $queryResults);
    }

    private function extractResults(Query $query, array $instances): array
    {
        return map(function (Instance $instance) use ($query): ExtractionInstance {
            $relations = $this->findRelatedInstances($query->relations(), $instance);
            return (new Extractor($query, $instance, $relations))->extract();
        }, $instances);
    }

    private function findRelatedInstances(array $relations, Instance $instance): array
    {
        return reduce(function (array $acc, Query $query) use ($instance): array {
            $results = $this->extraction->findRelatedInstances(
                $instance->uuid(),
                $query->params()
            );
            $query->setPagination($results->pagination());
            $acc[] = (new RelationsResults($query->params()))
                ->setResults($results)
                ->setRelations($this->fillRelations($results, $query));
            return $acc;
        }, $relations, []);
    }

    private function fillRelations(Results $results, Query $query): array
    {
        return flat_map(function (Instance $instance) use ($query) {
            return $this->findRelatedInstances($query->relations(), $instance);
        }, $results->instances());
    }
}
