<?php

namespace Tests\Editora\Data;

use function Lambdish\Phunctional\map;

class InstanceArrayBuilder
{
    private array $languages = [
        ['language' => 'es'],
        ['language' => 'en'],
    ];
    private array $instance = [
        'class' => [
            'key' => null,
        ],
        'metadata' => [
            'uuid' => '',
            'key' => '',
            'publication' => [
                'status' => 'pending',
                'startPublishingDate' => '1989-03-08 09:00:00',
                'endPublishingDate' => null,
            ],
        ],
        'attributes' => [],
        'relations' => [],
    ];

    public function __construct(private readonly bool $input = true)
    {
    }

    public function addClassKey(string $classKey): self
    {
        $this->instance['class']['key'] = $classKey;
        return $this;
    }

    public function addClassRelations(string $relationKey, array $allowedClasses): self
    {
        $this->instance['class']['relations'][] = [
            'key' => $relationKey,
            'classes' => $allowedClasses,
        ];
        return $this;
    }

    public function addMetadata(string $uuid = null, string $key = null, ?array $publication = []): self
    {
        $this->instance['metadata'] = [
            'uuid' => $uuid,
            'key' => $key,
            'publication' => array_merge([
                'status' => 'pending',
                'startPublishingDate' => '1989-03-08 09:00:00',
                'endPublishingDate' => null,
            ], $publication ?? []),
        ];
        return $this;
    }

    public function addAttribute(
        string $key,
        string $type,
        array $values,
        array $fn = []
    ): self {
        if ($this->input) {
            $this->instance['attributes'][$key] = $this->attribute($key, $type, $values, $fn);
        } else {
            $this->instance['attributes'][] = $this->attribute($key, $type, $values, $fn);
        }
        return $this;
    }

    private function attribute(string $key, string $type, array $values, array $fn = []): array
    {
        return [
            'key' => $key,
            'type' => $type,
            'values' => map(static fn(array $value) => array_merge([
                'uuid' => null,
                'language' => '',
                'rules' => [],
                'configuration' => [],
                'value' => null,
                'extraData' => [],
            ], $value), ([] === $values) ? $this->languages : $values),
            'attributes' => array_reduce($fn, function (array $acc, callable $fn) {
                if ($this->input) {
                    $attribute = $fn($this);
                    $acc[$attribute['key']] = $attribute;
                } else {
                    $acc[] = $fn($this);
                }
                return $acc;
            }, []),
        ];
    }

    public function addSubAttribute(string $key, string $type, array $values, array $fn = []): array
    {
        return $this->attribute($key, $type, $values, $fn);
    }

    public function addRelation(string $key, array $values): self
    {
        if ($this->input) {
            $this->instance['relations'][$key] = $values;
        } else {
            $this->instance['relations'][] = [ 'key' => $key, 'instances' => $values ];
        }
        return $this;
    }

    public function build(): array
    {
        return $this->instance;
    }

    public function results(): array
    {
        $results = [
            'key' => $this->instance['metadata']['key'],
            'attributes' => [],
            'relations' => [],
        ];

        foreach($this->instance['attributes'] as $attribute) {
            foreach($attribute['values'] as $value) {
                $results['attributes'][$value['language']][] = [
                    'uuid' => $value['uuid'],
                    'key' => $attribute['key'],
                    'value' => $value['value'],
                    'attributes' => []
                ];
            }
        }

        return $results;
    }
}
