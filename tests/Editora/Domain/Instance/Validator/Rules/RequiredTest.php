<?php

namespace Tests\Editora\Domain\Instance\Validator\Rules;

use Omatech\MageCore\Editora\Domain\Attribute\Attribute;
use Omatech\MageCore\Editora\Domain\Attribute\AttributeCollection;
use Omatech\MageCore\Editora\Domain\Instance\Validator\Exceptions\RequiredValueException;
use Omatech\MageCore\Editora\Domain\Instance\Validator\Rules\Required;
use Omatech\MageCore\Editora\Domain\Value\Types\Value;
use Tests\TestCase;

class RequiredTest extends TestCase
{
    /** @test */
    public function givenAttributeWithoutValueAndRequiredRuleWhenValidateThenThrowException(): void
    {
        $this->expectException(RequiredValueException::class);

        $value = new Value('test', 'es', [
            'rules' => [ 'required' ],
            'configuration' => [],
        ]);

        $attributes = new AttributeCollection([
            new Attribute([ 'key' => 'key', 'type' => 'string',
                'values' => [$value],
                'attributes' => []
            ])
        ]);

        $rule = new Required($attributes, true);
        $rule->validate($value);
    }

    /** @test */
    public function givenAttributeWithValueAndRequiredRuleWhenValidateThenOk(): void
    {
        $value = new Value('test', 'es', [
            'rules' => [ 'required' ],
            'configuration' => [],
        ]);
        $value->fill(['value' => 'test']);

        $attributes = new AttributeCollection([
            new Attribute([ 'key' => 'key', 'type' => 'string',
                'values' => [$value],
                'attributes' => []
            ])
        ]);

        $rule = new Required($attributes, true);
        $rule->validate($value);
        $this->assertTrue(true);
    }

    /** @test */
    public function givenAttributeWithoutValueAndNonRequiredRuleWhenValidateThenOk(): void
    {
        $value = new Value('test', 'es', [
            'rules' => [ 'required' ],
            'configuration' => [],
        ]);

        $attributes = new AttributeCollection([
            new Attribute([ 'key' => 'key', 'type' => 'string',
                'values' => [$value],
                'attributes' => []
            ])
        ]);

        $rule = new Required($attributes, false);
        $rule->validate($value);
        $this->assertTrue(true);
    }
}