<?php declare(strict_types=1);

namespace Omatech\MageCore\Editora\Domain\Instance\Validator;

use Omatech\MageCore\Editora\Domain\Attribute\Attribute;
use Omatech\MageCore\Editora\Domain\Attribute\AttributeCollection;
use Omatech\MageCore\Editora\Domain\Clazz\Relation;
use Omatech\MageCore\Editora\Domain\Instance\InstanceRelation;
use Omatech\MageCore\Editora\Domain\Instance\Validator\Exceptions\InvalidRelationException;
use Omatech\MageCore\Editora\Domain\Instance\Validator\Exceptions\InvalidRuleException;
use Omatech\MageCore\Editora\Domain\Value\BaseValue;
use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\filter;
use function Lambdish\Phunctional\first;
use function Lambdish\Phunctional\search;

final class Validator
{
    public function validateAttributes(AttributeCollection $attributes): void
    {
        each(function (Attribute $attribute) use ($attributes): void {
            each(function (BaseValue $value) use ($attributes): void {
                $this->validateRules($attributes, $value);
            }, $attribute->values()->get());
            $this->validateAttributes($attribute->attributes());
        }, $attributes->get());
    }

    private function validateRules(AttributeCollection $attributes, BaseValue $value): void
    {
        each(static function (mixed $conditions, string $rule) use ($attributes, $value): void {
            $class = first(filter(static fn ($class) => class_exists($class), [
                'Omatech\\MageCore\\Editora\\Domain\\Instance\\Validator\\Rules\\' . ucfirst($rule),
                $rule,
            ])) ?? InvalidRuleException::withRule($rule);
            (new $class($attributes, $conditions))->validate($value);
        }, $value->rules());
    }

    public function validateRelations(array $classRelation, array $instanceRelations): void
    {
        each(static function (InstanceRelation $instanceRelation) use ($classRelation): void {
            $relation = search(static function (Relation $relation) use ($instanceRelation): bool {
                return $relation->key() === $instanceRelation->key();
            }, $classRelation);
            if (is_null($relation)) {
                InvalidRelationException::withRelation($instanceRelation->key());
            }
            $relation->validate($instanceRelation->instances());
        }, $instanceRelations);
    }
}
