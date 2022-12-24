<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Value;

use Omatech\MageCore\Editora\Domain\Value\Exceptions\InvalidValueTypeException;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\first;
use function Lambdish\Phunctional\flat_map;
use function Lambdish\Phunctional\map;

final class ValueBuilder
{
    private array $languages;
    private string $key;
    private array $values;

    public function build(): array
    {
        $this->normalizeLanguages();
        $this->normalizeValues();
        return $this->instanceValues();
    }

    private function normalizeLanguages(): void
    {
        $this->values['languages'] ??= [];
        if (! array_key_exists('*', $this->values['languages'])) {
            if (array_key_exists('+', $this->values['languages'])) {
                $this->languages['+'] = [];
            }
            $this->values['languages'] = map(function ($values, $language): array {
                return $this->values['languages'][$language] ?? $values;
            }, $this->languages);
        }
    }

    private function normalizeValues(): void
    {
        $this->values = map(function ($properties): array {
            return $this->defaultsToValue($properties ?? []);
        }, $this->values['languages']);
    }

    private function defaultsToValue(array $properties): array
    {
        return map(function ($value, $key) use ($properties): string | array {
            return $properties[$key] ?? $this->values[$key] ?? $value;
        }, [
            'configuration' => [],
            'rules' => [],
            'type' => 'Value',
        ]);
    }

    private function instanceValues(): array
    {
        return flat_map(function ($properties, $language): BaseValue {
            $class = first(filter(static fn ($class) => class_exists($class), [
                'Omatech\\MageCore\\Editora\\Domain\\Value\\Types\\' . $properties['type'],
                $properties['type'],
            ])) ?? InvalidValueTypeException::withType($properties['type']);
            return new $class($this->key, $language, $properties);
        }, $this->values);
    }

    public function setLanguages(array $languages): ValueBuilder
    {
        $this->languages = $languages;
        return $this;
    }

    public function setValues(array $values): ValueBuilder
    {
        $this->values = $values;
        return $this;
    }

    public function setKey(string $key): ValueBuilder
    {
        $this->key = $key;
        return $this;
    }
}
