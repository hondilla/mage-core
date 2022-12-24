<?php

namespace Tests\Editora\Domain\Value;

use Omatech\MageCore\Editora\Domain\Value\Types\Value;
use Tests\TestCase;

class ValueTest extends TestCase
{
    /** @test */
    public function givenValueWhenFilledThenOk(): void
    {
        $value = new Value('test', 'es', [
            'rules' => [],
            'configuration' => [],
        ]);
        $value->fill([
            'value' => 'hola',
            'extraData' => [
                'ext' => 'jpeg',
            ],
            'uuid' => '1',
        ]);
        $this->assertEquals('1', $value->uuid());
        $this->assertEquals([
            'ext' => 'jpeg',
        ], $value->extraData());
        $this->assertEquals('hola', $value->value());
    }

    /** @test */
    public function givenValueWhenUpdateThenOk(): void
    {
        $value = new Value('test', 'es', [
            'rules' => [],
            'configuration' => [],
        ]);
        $value->fill([
            'value' => 'hola',
            'extraData' => [
                'ext' => 'jpeg',
            ],
            'uuid' => '1',
        ]);
        $this->assertEquals('1', $value->uuid());
        $value->fill(['uuid' => '2']);
        $this->assertEquals('2', $value->uuid());
    }
}