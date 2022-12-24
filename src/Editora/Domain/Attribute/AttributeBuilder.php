<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Attribute;

use Omatech\MageCore\Editora\Domain\Value\ValueBuilder;
use Omatech\MageCore\Shared\Utils\Utils;
use function Lambdish\Phunctional\flat_map;

final class AttributeBuilder
{
    private array $languages;
    private array $attributes;

    public function build(): array
    {
        return flat_map(function (?array $properties, string $key): Attribute {
            $properties = $this->defaultsToAttribute($properties, $key);
            $properties['values'] = (new ValueBuilder())
                ->setLanguages($this->languages)
                ->setValues($properties['values'])
                ->setKey($properties['key'])
                ->build();
            $properties['attributes'] = (new AttributeBuilder())
                ->setLanguages($this->languages)
                ->setAttributes($properties['attributes'])
                ->build();
            return new Attribute($properties);
        }, $this->attributes);
    }

    private function defaultsToAttribute(?array $properties, string $key): array
    {
        return [
            'key' => Utils::slug($key),
            'type' => $properties['type'] ?? 'string',
            'values' => $properties['values'] ?? [],
            'attributes' => $properties['attributes'] ?? [],
        ];
    }

    public function setAttributes(array $attributes): AttributeBuilder
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function setLanguages(array $languages): AttributeBuilder
    {
        $this->languages = $languages;
        return $this;
    }
}
