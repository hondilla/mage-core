<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use Omatech\MageCore\Editora\Domain\Instance\InstanceRelation;
use Tests\TestCase;

final class InstanceRelationTest extends TestCase
{
    /** @test */
    public function givenInstanceRelationWhenInstanceExistsThenOk(): void
    {
        $relationInstance = new InstanceRelation('relation-key1', [
            '1' => 'class-one',
            '2' => 'class-two',
            '3' => 'class-three',
            '4' => 'class-four',
        ]);
        $this->assertTrue($relationInstance->instanceExists('1'));
    }
}
