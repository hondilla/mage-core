<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use Mockery;
use Omatech\MageCore\Editora\Domain\Instance\Contracts\InstanceCacheInterface;
use Omatech\MageCore\Editora\Domain\Instance\Exceptions\InvalidClassNameException;
use Omatech\MageCore\Editora\Domain\Instance\Exceptions\InvalidLanguagesException;
use Omatech\MageCore\Editora\Domain\Instance\Exceptions\InvalidStructureException;
use Omatech\MageCore\Editora\Domain\Value\Exceptions\InvalidValueTypeException;
use Tests\Editora\Data\InstanceArrayBuilder;
use Tests\Editora\Data\InstanceBuilder;
use Tests\Editora\Data\UniqueValueRepository;
use Tests\TestCase;

class InstanceBuilderTest extends TestCase
{
    /** @test */
    public function givenEmptyLanguagesWhenBuilderInstanceBuildThenThrowException(): void
    {
        $this->expectException(InvalidLanguagesException::class);

        (new InstanceBuilder())
            ->setInstanceCache($this->mockNeverCalledInstanceCache())
            ->setLanguages([])
            ->build();
    }

    /** @test */
    public function givenEmptyStructureWhenBuilderInstanceBuildThenThrowException(): void
    {
        $this->expectException(InvalidStructureException::class);

        (new InstanceBuilder())
            ->setInstanceCache($this->mockNeverCalledInstanceCache())
            ->setStructure([])
            ->build();
    }

    /** @test */
    public function givenEmptyClassNameWhenbuilderInstanceBuildThenThrowException(): void
    {
        $this->expectException(InvalidClassNameException::class);

        (new InstanceBuilder())
            ->setInstanceCache($this->mockNeverCalledInstanceCache())
            ->setClassName('')
            ->build();
    }

    /** @test */
    public function givenInvalidValueInStructureWhenBuilderInstanceBuildThenThrowException(): void
    {
        $this->expectException(InvalidValueTypeException::class);

        (new InstanceBuilder())
            ->setInstanceCache($this->mockGetCalledInstanceCache())
            ->setStructure([
                'attributes' => [
                    'Invalid' => [
                        'values' => [
                            'type' => 'Invalid',
                        ],
                    ],
                ],
            ])
            ->build();
    }

    /** @test */
    public function givenInstanceBuilderWhenBuildThenOk(): void
    {
        $instance = (new InstanceBuilder())
            ->setInstanceCache($this->mockInstanceCache())
            ->build();

        $instanceArray = (new InstanceArrayBuilder(false))
            ->addMetadata(null, null, ['startPublishingDate' => null])
            ->addClassKey('video-games')
            ->addClassRelations('platforms', ['platform'])
            ->addClassRelations('reviews', ['articles', 'blogs'])
            ->addAttribute('title', 'string', [], [
                fn (InstanceArrayBuilder $builder) => $builder->addSubAttribute('code', 'string', []),
                fn (InstanceArrayBuilder $builder) => $builder->addSubAttribute('sub-title', 'string', [
                    ['language' => 'es', 'rules' => ['required' => true]],
                    ['language' => 'en', 'rules' => ['required' => true]],
                ]),
            ])
            ->addAttribute('sub-title', 'string', [
                ['language' => 'es', 'rules' => ['uniqueDB' => ['class' => UniqueValueRepository::class]]],
                ['language' => 'en', 'rules' => ['uniqueDB' => ['class' => UniqueValueRepository::class]]],
            ])
            ->addAttribute('synopsis', 'text', [
                ['language' => 'es', 'rules' => ['required' => true], 'configuration' => ['cols' => 10, 'rows' => 10]],
                ['language' => 'en', 'rules' => ['required' => true], 'configuration' => ['cols' => 10, 'rows' => 10]],
            ])
            ->addAttribute('release-date', 'string', [
                ['language' => 'es', 'rules' => ['required' => true], 'configuration' => ['cols' => 10, 'rows' => 10]],
                ['language' => 'en', 'rules' => ['required' => false], 'configuration' => ['cols' => 20, 'rows' => 20]],
                ['language' => '+', 'rules' => ['required' => true], 'configuration' => ['cols' => 30, 'rows' => 30]],
            ])
            ->addAttribute('code', 'lookup', [
                [
                    'language' => '*', 'rules' => ['required' => true, 'unique' => []],
                    'configuration' => ['options' => ['pc-code', 'playstation-code', 'xbox-code', 'switch-code']],
                ],
            ])
            ->build();

        $this->assertEquals($instanceArray, $instance->toArray());
    }

    private function mockNeverCalledInstanceCache(): InstanceCacheInterface
    {
        $mock = Mockery::mock(InstanceCacheInterface::class);
        $mock->shouldReceive('get')->never();
        $mock->shouldReceive('put')->never();
        return $mock;
    }

    private function mockGetCalledInstanceCache(): InstanceCacheInterface
    {
        $mock = Mockery::mock(InstanceCacheInterface::class);
        $mock->shouldReceive('get')->once()->andReturn(null);
        $mock->shouldReceive('put')->never();
        return $mock;
    }

    private function mockInstanceCache(): InstanceCacheInterface
    {
        $mock = Mockery::mock(InstanceCacheInterface::class);
        $mock->shouldReceive('get')->once()->andReturn(null);
        $mock->shouldReceive('put')->once()->andReturn(null);
        return $mock;
    }
}
