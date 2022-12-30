<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Extraction;

use function Lambdish\Phunctional\reduce;

final class Attribute extends QueryAttribute
{
    private ?string $uuid = null;
    private mixed $value = null;

    public function __construct(string $key, Value $value, array $attributes)
    {
        parent::__construct($key, $attributes);
        $this->uuid = $value->uuid();
        $this->value = $value->value();
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'value' => $this->value,
            'attributes' => reduce(static function (array $acc, self $attribute): array {
                $acc[$attribute->key()] = $attribute->toArray();
                return $acc;
            }, $this->attributes, []),
        ];
    }
}
