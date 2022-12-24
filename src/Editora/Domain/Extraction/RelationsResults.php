<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

final class RelationsResults
{
    private readonly string $key;
    private readonly string $type;
    private Results $results;
    private array $relations;

    public function __construct(array $params)
    {
        $this->key = $params['key'];
        $this->type = $params['type'];
    }

    public function setResults(Results $results): RelationsResults
    {
        $this->results = $results;
        return $this;
    }

    public function setRelations(array $relations): RelationsResults
    {
        $this->relations = $relations;
        return $this;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function instances(): array
    {
        return $this->results->instances();
    }

    public function relations(): array
    {
        return $this->relations;
    }
}
