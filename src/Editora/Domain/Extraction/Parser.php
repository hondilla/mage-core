<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

use GraphQL\Language\AST\ArgumentNode;
use GraphQL\Language\AST\FieldNode;
use GraphQL\Language\AST\NodeList;
use GraphQL\Language\Parser as GraphQLParser;
use Omatech\MageCore\Shared\Utils\Utils;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\search;

final class Parser
{
    public function parse(string $query): array
    {
        $graphQuery = GraphQLParser::parse(str_replace('()', '(limit: 0)', $query));
        return reduce(function (array $acc, FieldNode $node): array {
            $acc[] = $this->parseRootNode($node);
            return $acc;
        }, $graphQuery->definitions[0]->toArray()['selectionSet']->selections, []);
    }

    private function parseRootNode(FieldNode $node): Query
    {
        $params = $this->parseParams($node, 'class');
        return new Query([
            'attributes' => $this->parseAttributes($node),
            'params' => $params,
            'relations' => $this->parseRelations($node, [
                'languages' => $params['languages'],
                'preview' => $params['preview'],
            ]),
        ]);
    }

    private function parseParams(FieldNode $node, string $nodeType): array
    {
        $params = reduce(function (array $acc, ArgumentNode $argument): array {
            $acc[$argument->name->value] = $argument->value->value ??
                $this->parseArrayParams($argument->value->values);
            return $acc;
        }, $node->arguments, []);
        if ($node->name->value !== 'instances') {
            $params[$nodeType] = $node->name->value;
        }
        $params['class'] = Utils::slug($params['class'] ?? null);
        $params['key'] = Utils::slug($params['key'] ?? null);
        $params['preview'] ??= false;
        $params['limit'] = (int) ($params['limit'] ?? 0);
        $params['page'] = (int) ($params['page'] ?? 1);
        $params['languages'] = $this->parseLanguages($params['languages'] ?? []);
        return $params;
    }

    private function parseArrayParams(NodeList $values): array
    {
        return reduce(static function (array $acc, $value) {
            $acc[] = $value->value;
            return $acc;
        }, $values, []);
    }

    private function parseLanguages(string|array $value): array
    {
        if (is_string($value)) {
            return [$value];
        }
        return $value;
    }

    private function parseAttributes(FieldNode $node): array
    {
        return reduce(function (array $acc, FieldNode $node): array {
            if (count($node->arguments) === 0) {
                $acc[] = new QueryAttribute(
                    Utils::slug($node->name->value),
                    $this->parseAttributes($node)
                );
            }
            return $acc;
        }, $node->toArray()['selectionSet']->selections ?? [], []);
    }

    private function parseRelations(FieldNode $node, array $params = []): array
    {
        return reduce(function (array $acc, FieldNode $node) use ($params): array {
            if (count($node->arguments) > 0) {
                $acc[] = new Query([
                    'attributes' => $this->parseAttributes($node),
                    'params' => $this->defaultRelationParams([
                        ...$this->parseParams($node, 'key'),
                        ...$params,
                    ]),
                    'relations' => $this->parseRelations($node, $params),
                ]);
            }
            return $acc;
        }, $node->toArray()['selectionSet']->selections ?? [], []);
    }

    private function defaultRelationParams(array $params): array
    {
        $params['type'] ??= 'child';
        $params['type'] = search(static function (string $type) use ($params): bool {
            return $type === $params['type'];
        }, ['parent'], 'child');
        return $params;
    }
}
