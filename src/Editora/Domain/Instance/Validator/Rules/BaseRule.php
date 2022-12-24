<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance\Validator\Rules;

use Omatech\MageCore\Editora\Domain\Attribute\AttributeCollection;
use Omatech\MageCore\Editora\Domain\Value\BaseValue;

abstract class BaseRule
{
    public function __construct(
        protected AttributeCollection $attributeCollection,
        protected mixed $conditions
    ) {
    }

    abstract public function validate(BaseValue $value): void;
}
