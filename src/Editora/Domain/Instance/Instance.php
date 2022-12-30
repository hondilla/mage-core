<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance;

use Omatech\MageCore\Editora\Domain\Attribute\AttributeCollection;
use Omatech\MageCore\Editora\Domain\Clazz\Clazz;
use Omatech\MageCore\Editora\Domain\Instance\Validator\Validator;

class Instance
{
    private Clazz $clazz;
    private Metadata $metadata;
    private AttributeCollection $attributesCollection;
    private InstanceRelationCollection $instanceRelationCollection;

    public function __construct(array $instance)
    {
        $this->clazz = new Clazz($instance['metadata']);
        $this->metadata = new Metadata();
        $this->attributesCollection = new AttributeCollection($instance['attributes']);
        $this->instanceRelationCollection = new InstanceRelationCollection();
    }

    public function fill(array $instance): self
    {
        assert(isset($instance['attributes']));
        assert(isset($instance['relations']));
        $this->metadata->fill($instance['metadata'] ?? $this->metadata->toArray());
        $this->attributesCollection->fill($instance['attributes']);
        $this->instanceRelationCollection->fill($instance['relations']);
        $this->validate();
        return $this;
    }

    private function validate(): void
    {
        (new Validator())->validateAttributes($this->attributesCollection);
        (new Validator())->validateRelations(
            $this->clazz->relations()->get(),
            $this->instanceRelationCollection->get()
        );
    }

    public function uuid(): ?string
    {
        return $this->metadata->uuid();
    }

    public function key(): string
    {
        return $this->metadata->key();
    }

    public function data(): array
    {
        return [
            'classKey' => $this->clazz->key(),
        ] + $this->metadata->data();
    }

    public function attributes(): AttributeCollection
    {
        return $this->attributesCollection;
    }

    public function relations(): InstanceRelationCollection
    {
        return $this->instanceRelationCollection;
    }

    public function toArray(): array
    {
        return [
            'class' => $this->clazz->toArray(),
            'metadata' => $this->metadata->toArray(),
            'attributes' => $this->attributesCollection->toArray(),
            'relations' => $this->instanceRelationCollection->toArray(),
        ];
    }
}
