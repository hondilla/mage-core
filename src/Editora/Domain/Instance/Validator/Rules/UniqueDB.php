<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance\Validator\Rules;

use Omatech\MageCore\Editora\Domain\Attribute\AttributeCollection;
use Omatech\MageCore\Editora\Domain\Instance\Validator\Contracts\UniqueValueInterface;
use Omatech\MageCore\Editora\Domain\Instance\Validator\Exceptions\UniqueValueInDBException;
use Omatech\MageCore\Editora\Domain\Value\BaseValue;

final class UniqueDB extends Unique
{
    private readonly UniqueValueInterface $repository;

    public function __construct(AttributeCollection $attributeCollection, mixed $conditions)
    {
        $this->repository = new $conditions['class']();
        parent::__construct($attributeCollection, $conditions);
    }

    public function validate(BaseValue $value): void
    {
        parent::validate($value);
        $this->validateInDB($value);
    }

    private function validateInDB(BaseValue $value): void
    {
        if (! $this->repository->isUnique($value)) {
            UniqueValueInDBException::withValue($value);
        }
    }
}
