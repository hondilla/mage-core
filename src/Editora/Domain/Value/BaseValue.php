<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Value;

abstract class BaseValue
{
    protected ?string $uuid = null;
    protected mixed $value = null;
    protected array $extraData = [];
    protected Configuration $configuration;
    private Metadata $metadata;

    public function __construct(string $attributeKey, string $language, array $properties)
    {
        $this->metadata = new Metadata($attributeKey, $language, $properties['rules']);
        $this->configuration = new Configuration($properties['configuration']);
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function extraData(): array
    {
        return $this->extraData;
    }

    public function fill(array $value): void
    {
        $this->value = $value['value'] ?? $this->value;
        $this->extraData = $value['extraData'] ?? $this->extraData;
        $this->uuid = $value['uuid'] ?? $this->uuid;
    }

    public function language(): string
    {
        return $this->metadata->language();
    }

    public function rules(): array
    {
        return $this->metadata->rules();
    }

    public function uuid(): ?string
    {
        return $this->uuid;
    }

    public function key(): string
    {
        return $this->metadata->attributeKey();
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'language' => $this->metadata->language(),
            'rules' => $this->metadata->rules(),
            'configuration' => $this->configuration->get(),
            'value' => $this->value,
            'extraData' => $this->extraData,
        ];
    }
}
