<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

use Omatech\MageCore\Editora\Domain\Attribute\Attribute;
use Omatech\MageCore\Editora\Domain\Attribute\AttributeCollection as InstanceAttributes;
use Omatech\MageCore\Editora\Domain\Extraction\Attribute as ExtractionAttribute;
use Omatech\MageCore\Editora\Domain\Extraction\Instance as ExtractionInstance;
use Omatech\MageCore\Editora\Domain\Extraction\Value as ExtractionValue;
use Omatech\MageCore\Editora\Domain\Instance\Instance;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\first;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\search;

final readonly class Extractor
{
    public function __construct(
        private Query $query,
        private Instance $instance,
        private array $relations = []
    ) {
    }

    public function extract(): ExtractionInstance
    {
        return $this->extractInstance($this->query, $this->instance, $this->relations);
    }

    private function extractInstance(
        Query $query,
        Instance $instance,
        array $instanceRelations = []
    ): ExtractionInstance {
        return new ExtractionInstance([
            'class' => $instance->data()['classKey'],
            'key' => $instance->key(),
            'attributes' => $this->extractAttributes(
                $query->attributes(),
                $instance->attributes(),
            ),
            'relations' => $this->extractRelations($query->relations(), $instanceRelations),
        ]);
    }

    private function extractAttributes(
        array $queryAttributes,
        InstanceAttributes $instanceAttributes
    ): array {
        return reduce(function (
            array $acc,
            string $language
        ) use ($queryAttributes, $instanceAttributes) {
            $acc[$language] = $this->extractAttributesByLanguage(
                $language,
                $queryAttributes,
                $instanceAttributes
            );
            return $acc;
        }, $this->query->languages(), []);
    }

    private function extractAttributesByLanguage(
        string $language,
        array $queryAttributes,
        InstanceAttributes $instanceAttributes
    ): array {
        return reduce(function (
            array $acc,
            Attribute $instanceAttribute
        ) use ($language, $queryAttributes): array {
            $queryAttribute = $this->searchForQueryAttribute($queryAttributes, $instanceAttribute);
            if ($queryAttribute !== null) {
                $acc[] = $this->fillValueForQueryAttribute(
                    $language,
                    $instanceAttribute,
                    $queryAttribute
                );
            }
            return $acc;
        }, $instanceAttributes->get(), []);
    }

    private function searchForQueryAttribute(
        array $queryAttributes,
        Attribute $instanceAttribute
    ): ?QueryAttribute {
        $queryAttribute = search(static function (
            QueryAttribute $queryAttribute
        ) use ($instanceAttribute): bool {
            return $instanceAttribute->key() === $queryAttribute->key();
        }, $queryAttributes);
        if ($queryAttributes === []) {
            return new QueryAttribute($instanceAttribute->key(), []);
        }
        return $queryAttribute;
    }

    private function fillValueForQueryAttribute(
        string $language,
        Attribute $instanceAttribute,
        QueryAttribute $queryAttribute
    ): ExtractionAttribute {
        $value = $this->extractValue($instanceAttribute, $language);
        $attributes = $this->extractAttributesByLanguage(
            $language,
            $queryAttribute->attributes(),
            $instanceAttribute->attributes()
        );
        return new ExtractionAttribute($queryAttribute->key(), $value, $attributes);
    }

    private function extractValue(Attribute $attribute, string $language): ExtractionValue
    {
        $values = [
            $language => $attribute->values()->language($language)?->value(),
            '*' => $attribute->values()->language('*')?->value(),
            '+' => $attribute->values()->language('+')?->value(),
        ];

        return new Value(
            $attribute->values()->language($language)?->uuid(),
            first(filter(static fn ($value) => ! is_null($value), $values))
        );
    }

    private function extractRelations(array $queryRelations, array $relations): array
    {
        return reduce(function (
            array $acc,
            RelationsResults $relation
        ) use ($queryRelations): array {
            $queryRelation = search(static function ($query) use ($relation): bool {
                if ($query->param('key') !== $relation->key()) {
                    return false;
                }
                return $query->param('type') === $relation->type();
            }, $queryRelations);
            if (isset($queryRelation)) {
                $acc[] = (new Relation($relation->key(), $relation->type()))
                    ->setInstances($this->addInstancesRelation($queryRelation, $relation));
            }
            return $acc;
        }, $relations, []);
    }

    private function addInstancesRelation(Query $queryRelation, RelationsResults $relation): array
    {
        return reduce(
            function (array $acc, Instance $instance) use ($queryRelation, $relation): array {
                $acc[] = $this->extractInstance($queryRelation, $instance, $relation->relations());
                return $acc;
            },
            $relation->instances(),
            []
        );
    }
}
