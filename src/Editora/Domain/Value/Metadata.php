<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Value;

final class Metadata
{
    public function __construct(
        private string $attributeKey,
        private string $language,
        private array $rules
    ) {
    }

    public function attributeKey(): string
    {
        return $this->attributeKey;
    }

    public function language(): string
    {
        return $this->language;
    }

    public function rules(): array
    {
        return $this->rules;
    }
}
