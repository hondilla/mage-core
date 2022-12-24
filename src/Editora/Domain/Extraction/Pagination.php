<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

final readonly class Pagination
{
    private int $limit;
    private int $current;
    private int $pages;

    public function __construct(array $params, private int $total)
    {
        $this->limit = $params['limit'] > 0 ? $params['limit'] : $total;
        $this->current = $params['page'];
        $this->pages = ($this->limit > 0) ? (int) ceil($this->total / $this->limit) : 0;
    }

    public function limit(): int
    {
        return $this->limit;
    }

    public function offset(): int
    {
        return ($this->current - 1) * $this->limit;
    }

    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'limit' => $this->limit,
            'current' => $this->current,
            'pages' => $this->pages,
        ];
    }
}
