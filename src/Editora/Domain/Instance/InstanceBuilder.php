<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance;

use Omatech\MageCore\Editora\Domain\Attribute\AttributeBuilder;
use Omatech\MageCore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Omatech\MageCore\Editora\Domain\Instance\Exceptions\InvalidClassNameException;
use Omatech\MageCore\Editora\Domain\Instance\Exceptions\InvalidLanguagesException;
use Omatech\MageCore\Editora\Domain\Instance\Exceptions\InvalidStructureException;
use Omatech\MageCore\Shared\Utils\Utils;
use function Lambdish\Phunctional\map;

final class InstanceBuilder
{
    private array $languages = [];
    private array $structure = [];
    private string $className = '';

    public function __construct(private readonly InstanceCacheInterface $instanceCache)
    {
    }

    public function build(): Instance
    {
        $this->ensureBuilderIsValid();
        return $this->instanceCache->get($this->className) ?? $this->buildInstance();
    }

    private function ensureBuilderIsValid(): void
    {
        if ($this->languages === []) {
            throw new InvalidLanguagesException();
        }
        if ($this->className === '') {
            throw new InvalidClassNameException();
        }
        if ($this->structure === []) {
            throw new InvalidStructureException();
        }
    }

    private function buildInstance(): Instance
    {
        $instance = [
            'metadata' => [
                'key' => $this->className,
                'relations' => $this->normalizeRelations(),
            ],
            'attributes' => (new AttributeBuilder())
                ->setLanguages($this->languages)
                ->setAttributes($this->structure['attributes'])
                ->build(),
        ];

        $instance = new Instance($instance);
        $this->instanceCache->put($this->className, $instance);
        return $instance;
    }

    private function normalizeRelations(): array
    {
        return map(static function (array $relations, string &$key): array {
            $key = Utils::slug($key);
            return map(static fn ($class): string => Utils::slug($class), $relations);
        }, $this->structure['relations'] ?? []);
    }

    public function setLanguages(array $languages): InstanceBuilder
    {
        $this->languages = array_fill_keys($languages, []);
        return $this;
    }

    public function setStructure(array $structure): InstanceBuilder
    {
        $this->structure = $structure;
        return $this;
    }

    public function setClassName(string $className): InstanceBuilder
    {
        $this->className = Utils::slug($className);
        return $this;
    }
}
