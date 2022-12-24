<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Instance;

use DateTimeImmutable;
use Omatech\MageCore\Editora\Domain\Clazz\Exceptions\InvalidRelationClassException;
use Omatech\MageCore\Editora\Domain\Instance\Exceptions\InvalidEndDatePublishingException;
use Omatech\MageCore\Editora\Domain\Instance\PublicationStatus;
use Omatech\MageCore\Editora\Domain\Instance\Validator\Exceptions\InvalidRelationException;
use Omatech\MageCore\Editora\Domain\Instance\Validator\Exceptions\InvalidRuleException;
use Omatech\MageCore\Editora\Domain\Instance\Validator\Exceptions\RequiredValueException;
use Omatech\MageCore\Editora\Domain\Instance\Validator\Exceptions\UniqueValueException;
use Omatech\MageCore\Editora\Domain\Instance\Validator\Exceptions\UniqueValueInDBException;
use Omatech\MageCore\Editora\Domain\Value\Exceptions\LookupValueOptionException;
use Tests\Editora\Data\InstanceArrayBuilder;
use Tests\Editora\Data\InstanceFactory;
use Tests\Editora\Data\UniqueValueRepository;
use Tests\Editora\Data\VideoGamesArrayBuilder;
use Tests\TestCase;
use function Lambdish\Phunctional\search;

final class InstanceTest extends TestCase
{
    /** @test */
    public function givenInstanceWhenFillInstanceWithoutRequiredValueThenThrowException(): void
    {
        $this->expectException(RequiredValueException::class);

        InstanceFactory::fill('VideoGames', static fn(InstanceArrayBuilder $builder) => $builder->build());
    }

    /** @test */
    public function givenInstanceWhenFillInstanceWithInvalidLookupOptionsThenThrowException(): void
    {
        $this->expectException(LookupValueOptionException::class);

        InstanceFactory::fill('VideoGames', static fn(InstanceArrayBuilder $builder) => $builder->addAttribute('code', 'string', [
            ['language' => '*', 'uuid' => '1', 'value' => 'hola'],
        ])->build());
    }

    /** @test */
    public function givenInstanceWhenFillInstanceWithInvalidRuleThenThrowException(): void
    {
        $this->expectException(InvalidRuleException::class);

        InstanceFactory::fill('Movies', static fn(InstanceArrayBuilder $builder) => $builder->build());
    }

    /** @test */
    public function givenInstanceWhenFillInstanceWithoutRequiredValueInSubAttributeThenThrowException(): void
    {
        $this->expectException(RequiredValueException::class);

        $instanceArrayBuilder = VideoGamesArrayBuilder::withAttributes()
            ->addAttribute('title', 'string', [
                ['language' => 'es', 'value' => 'titulo'],
                ['language' => 'en', 'value' => 'title'],
            ], [
                fn (InstanceArrayBuilder $builder) => $builder->addSubAttribute('sub-title', 'string', [
                    ['language' => 'es'],
                    ['language' => 'en'],
                ])
            ]);
        InstanceFactory::fill('VideoGames', static fn () => $instanceArrayBuilder->build());
    }

    /** @test */
    public function givenInstanceWhenFillInstanceWithoutValidRelationsThenThrowException(): void
    {
        $this->expectException(InvalidRelationException::class);

        $instanceArrayBuilder = VideoGamesArrayBuilder::withAttributes()
            ->addRelation('non-valid-relation', [
                '1' => 'class-two',
                '2' => 'class-two',
                '3' => 'class-two',
            ]
        );
        InstanceFactory::fill('VideoGames', static fn () => $instanceArrayBuilder->build());
    }

    /** @test */
    public function givenInstanceWhenFilledInstanceWithoutValidRelationInstanceThenThrowException(): void
    {
        $this->expectException(InvalidRelationClassException::class);

        $instanceArrayBuilder = VideoGamesArrayBuilder::withAttributes()
            ->addRelation('platforms', [ '1' => 'non-valid-class' ]);
        InstanceFactory::fill('VideoGames', static fn () => $instanceArrayBuilder->build());
    }

    /** @test */
    public function givenInstanceWhenEndDateIsLessThanStartDatePublishingThenThrowException(): void
    {
        $this->expectException(InvalidEndDatePublishingException::class);

        $instanceArrayBuilder = VideoGamesArrayBuilder::withAttributes()
            ->addMetadata(null, 'instance', [
                'status' => PublicationStatus::REVISION,
                'startPublishingDate' => '2022-03-08 09:00:00',
                'endPublishingDate' => '2021-07-27 14:30:00',
            ]);
        InstanceFactory::fill('VideoGames', static fn () => $instanceArrayBuilder->build());
    }

    /** @test */
    public function givenInstanceWhenEndDateIsEqualToStartDatePublishingThenThrowException(): void
    {
        $this->expectException(InvalidEndDatePublishingException::class);

        $instanceArrayBuilder = VideoGamesArrayBuilder::withAttributes()
            ->addMetadata(null, 'instance', [
                'status' => PublicationStatus::REVISION,
                'startPublishingDate' => '2022-03-08 09:00:00',
                'endPublishingDate' => '2022-03-08 09:00:00',
            ]);
        InstanceFactory::fill('VideoGames', static fn () => $instanceArrayBuilder->build());
    }

    /** @test */
    public function givenInstanceWhenInstanceFillWithSameValueOnUniqueRuleThenThrowException(): void
    {
        $this->expectException(UniqueValueException::class);

        $instanceArrayBuilder = VideoGamesArrayBuilder::withAttributes()
            ->addAttribute('title', 'string', [
                ['uuid' => 'c342193b-1c16-3077-af26-84cf15acc9a2', 'language' => 'es', 'value' => 'titulo'],
                ['uuid' => 'f3078d87-e366-3506-9077-d24a872af11c', 'language' => 'en', 'value' => 'title'],
            ], [
                fn (InstanceArrayBuilder $builder) => $builder->addSubAttribute('code', 'string', [
                    ['40f91d9f-51c2-36a2-815c-922e20136bee', 'language' => 'es', 'value' => 'playstation-code'],
                    ['818e45fc-6d6c-3352-a69b-37c1ebf720a2', 'language' => 'en', 'value' => 'playstation-code'],
                ]),
                fn (InstanceArrayBuilder $builder) => $builder->addSubAttribute('sub-title', 'string', [
                    ['65b12889-81d8-3068-a1ca-202dcd3ee4a6', 'language' => 'es', 'value' => 'sub-sub-titulo'],
                    ['84968337-90d0-3412-b33f-5afd23e39c9f', 'language' => 'en', 'value' => 'sub-sub-title'],
                ]),
            ]);
        InstanceFactory::fill('VideoGames', static fn () => $instanceArrayBuilder->build());
    }

    /** @test */
    public function givenInstanceWhenInstanceFillWithSameValueOnUniqueDBRuleThenThrowException(): void
    {
        $this->expectException(UniqueValueInDBException::class);

        $instanceArrayBuilder = VideoGamesArrayBuilder::withAttributes()
            ->addAttribute('sub-title', 'string', [
                ['uuid' => 'fake-uuid', 'language' => 'es', 'value' => 'sub-titulo'],
                ['uuid' => 'fake-uuid-two', 'language' => 'en', 'value' => 'sub-title'],
            ]);
        InstanceFactory::fill('VideoGames', static fn () => $instanceArrayBuilder->build());
    }

    /** @test */
    public function givenInstanceWhenInstanceFillWithSameValueOnUniqueRuleAndUniqueDBRuleThenThrowException(): void
    {
        $this->expectException(UniqueValueException::class);

        $instanceArrayBuilder = VideoGamesArrayBuilder::withAttributes()
            ->addAttribute('sub-title', 'string', [
                ['uuid' => 'fake-uuid', 'language' => 'es', 'value' => 'sub-sub-titulo'],
                ['uuid' => 'fake-uuid-two', 'language' => 'en', 'value' => 'sub-sub-title'],
            ]);
        InstanceFactory::fill('VideoGames', static fn() => $instanceArrayBuilder->build());
    }

    /** @test */
    public function givenInstanceWhenFilledWithNonValidAttributeThenAttributeDoesNotFoundInInstanceSpecification(): void
    {
        $instanceArrayBuilder = VideoGamesArrayBuilder::withAttributes()
            ->addAttribute('non-attribute', 'string', []);
        $instance = InstanceFactory::fill('VideoGames', static fn() => $instanceArrayBuilder->build());

        $results = search(static fn(array $attribute) => $attribute['key'] === 'non-attribute', $instance->toArray()['attributes']);

        $this->assertNull($results);
    }

    /** @test */
    public function givenInstanceWhenFilledWithNonValidLanguageThenLanguageDoesNotFoundInInstanceSpecification(): void
    {
        $instanceArrayBuilder = VideoGamesArrayBuilder::withAttributes()
            ->addAttribute('synopsis', 'string', [
                ['language' => 'es', 'value' => 'hola mundo'],
                ['language' => 'en', 'value' => 'hello world'],
                ['language' => 'jp', 'value' => 'こんにちは世界'],
            ]);
        $instance = InstanceFactory::fill('VideoGames', static fn() => $instanceArrayBuilder->build());

        $results = search(static fn(array $attribute) => $attribute['key'] === 'synopsis', $instance->toArray()['attributes']);

        $results = search(static fn(array $value) => $value['language'] === 'jp', $results['values']);

        $this->assertNull($results);
    }

    /** @test */
    public function givenInstanceWhenFilledThenOk(): void
    {
        $instanceArrayBuilder = VideoGamesArrayBuilder::withAttributes()
            ->addMetadata('custom-uuid', 'video-game-instance', [
                'status' => PublicationStatus::REVISION,
                'startPublishingDate' => '1989-03-08 09:00:00',
                'endPublishingDate' => '2021-07-27 14:30:00',
            ])
            ->addRelation('platforms', [
                'be0deb29-910f-4559-9aea-0b9b1d152e20' => 'platform',
                '53ec408e-294a-4221-953f-dfc1aed08235' => 'platform',
                '7aaa7fa5-75ba-461d-8d06-a5ae756f2e3e' => 'platform',
                '0c187c3c-45ae-49f8-b9f4-85b4fc6b6f53' => 'platform',
                '332f8de2-5789-4234-8497-85dbc2e67dc1' => 'platform',
                'ef0b94ea-c042-43bd-9b12-cbc6c641be79' => 'platform',
            ])
            ->addRelation('reviews', [
                'ae72fe61-31eb-4811-bced-62418703791f' => 'articles',
                '69dff245-252a-4483-8006-4d53c685c66f' => 'articles',
                '7e271eb3-eba5-4ccb-b4d6-83fe00882848' => 'blogs',
                '504d84c5-af31-48ed-9efc-dd825b3f6708' => 'blogs',
                'c04694b3-8d59-4492-92a5-9730277aef9a' => 'blogs',
            ]);
        $instance = InstanceFactory::fill('VideoGames', static fn () => $instanceArrayBuilder->build());

        $this->assertEquals([
            'classKey' => 'video-games',
            'key' => 'video-game-instance',
            'status' => 'in-revision',
            'startPublishingDate' => DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '1989-03-08 09:00:00'),
            'endPublishingDate' => DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2021-07-27 14:30:00'),
        ], $instance->data());
        $this->assertEquals('custom-uuid', $instance->uuid());
        $this->assertIsArray($instance->attributes()->toArray());
        $this->assertEquals([
            [
                'key' => 'platforms',
                'instances' => [
                    'be0deb29-910f-4559-9aea-0b9b1d152e20' => 'platform',
                    '53ec408e-294a-4221-953f-dfc1aed08235' => 'platform',
                    '7aaa7fa5-75ba-461d-8d06-a5ae756f2e3e' => 'platform',
                    '0c187c3c-45ae-49f8-b9f4-85b4fc6b6f53' => 'platform',
                    '332f8de2-5789-4234-8497-85dbc2e67dc1' => 'platform',
                    'ef0b94ea-c042-43bd-9b12-cbc6c641be79' => 'platform',
                ],
            ], [
                'key' => 'reviews',
                'instances' => [
                    'ae72fe61-31eb-4811-bced-62418703791f' => 'articles',
                    '69dff245-252a-4483-8006-4d53c685c66f' => 'articles',
                    '7e271eb3-eba5-4ccb-b4d6-83fe00882848' => 'blogs',
                    '504d84c5-af31-48ed-9efc-dd825b3f6708' => 'blogs',
                    'c04694b3-8d59-4492-92a5-9730277aef9a' => 'blogs',
                ],
            ],
        ], $instance->relations()->toArray());

        $instanceArray = (new InstanceArrayBuilder(false))
            ->addClassKey('video-games')
            ->addClassRelations('platforms', ['platform'])
            ->addClassRelations('reviews', ['articles', 'blogs'])
            ->addMetadata('custom-uuid', 'video-game-instance', [
                'status' => PublicationStatus::REVISION,
                'startPublishingDate' => '1989-03-08 09:00:00',
                'endPublishingDate' => '2021-07-27 14:30:00',
            ])
            ->addAttribute('title', 'string', [
                ['uuid' => 'c342193b-1c16-3077-af26-84cf15acc9a2', 'language' => 'es', 'value' => 'titulo'],
                ['uuid' => 'f3078d87-e366-3506-9077-d24a872af11c', 'language' => 'en', 'value' => 'title'],
            ], [
                fn (InstanceArrayBuilder $builder) => $builder->addSubAttribute('code', 'string', [
                    ['uuid' => '40f91d9f-51c2-36a2-815c-922e20136bee', 'language' => 'es'],
                    ['uuid' => '818e45fc-6d6c-3352-a69b-37c1ebf720a2', 'language' => 'en'],
                ]),
                fn (InstanceArrayBuilder $builder) => $builder->addSubAttribute('sub-title', 'string', [
                    ['uuid' => '65b12889-81d8-3068-a1ca-202dcd3ee4a6', 'rules' => ['required' => true], 'language' => 'es', 'value' => 'sub-sub-titulo'],
                    ['uuid' => '84968337-90d0-3412-b33f-5afd23e39c9f', 'rules' => ['required' => true], 'language' => 'en', 'value' => 'sub-sub-title'],
                ]),
            ])
            ->addAttribute('sub-title', 'string', [
                ['uuid' => '8b105764-6fb3-3f94-a2a2-5360064cab40', 'language' => 'es', 'rules' => ['uniqueDB' => ['class' => UniqueValueRepository::class ]], 'value' => 'sub-titulo'],
                ['uuid' => '592f4da6-2243-34e0-af33-6c2b1a5ebabc', 'language' => 'en', 'rules' => ['uniqueDB' => ['class' => UniqueValueRepository::class ]], 'value' => 'sub-title'],
            ])
            ->addAttribute('synopsis', 'text', [
                ['uuid' => 'bc011d62-60a1-3d3c-8983-9126bfa4261b', 'language' => 'es', 'rules' => [ 'required' => true ], 'configuration' => [ 'cols' => 10, 'rows' => 10 ], 'value' => 'sinopsis'],
                ['uuid' => 'ec28da68-3b91-3aeb-9d44-f08778576426', 'language' => 'en', 'rules' => [ 'required' => true ], 'configuration' => [ 'cols' => 10, 'rows' => 10 ], 'value' => 'synopsis']
            ])
            ->addAttribute('release-date', 'string', [
                ['uuid' => 'fe49b765-9b45-3b68-9467-c4b4a5154946', 'language' => 'es', 'rules' => [ 'required' => true ], 'configuration' => [ 'cols' => 10, 'rows' => 10 ], 'value' => 'fecha-salida'],
                ['uuid' => '798f58a9-df29-36f9-b4c8-3028c6ddcf2f', 'language' => 'en', 'rules' => [ 'required' => false ], 'configuration' => [ 'cols' => 20, 'rows' => 20 ], 'value' => 'release-date'],
                ['uuid' => 'de17be73-260a-38ed-bef4-f7fdfae85201', 'language' => '+', 'rules' => [ 'required' => true ], 'configuration' => [ 'cols' => 30, 'rows' => 30 ], 'value' => 'default-date' ]
            ])
            ->addAttribute('code', 'lookup', [
                [
                    'uuid' => 'f6e5c12e-99f4-3d5e-b8a8-0fc702b9fda7',
                    'language' => '*',
                    'rules' => [ 'required' => true, 'unique' => [] ],
                    'configuration' => [ 'options' => [ 'pc-code', 'playstation-code', 'xbox-code', 'switch-code' ] ],
                    'value' => 'playstation-code',
                ]
            ])
            ->addRelation('platforms', [
                'be0deb29-910f-4559-9aea-0b9b1d152e20' => 'platform',
                '53ec408e-294a-4221-953f-dfc1aed08235' => 'platform',
                '7aaa7fa5-75ba-461d-8d06-a5ae756f2e3e' => 'platform',
                '0c187c3c-45ae-49f8-b9f4-85b4fc6b6f53' => 'platform',
                '332f8de2-5789-4234-8497-85dbc2e67dc1' => 'platform',
                'ef0b94ea-c042-43bd-9b12-cbc6c641be79' => 'platform',
            ])
            ->addRelation('reviews', [
                'ae72fe61-31eb-4811-bced-62418703791f' => 'articles',
                '69dff245-252a-4483-8006-4d53c685c66f' => 'articles',
                '7e271eb3-eba5-4ccb-b4d6-83fe00882848' => 'blogs',
                '504d84c5-af31-48ed-9efc-dd825b3f6708' => 'blogs',
                'c04694b3-8d59-4492-92a5-9730277aef9a' => 'blogs',
            ]);

        $this->assertEquals($instanceArray->build(), $instance->toArray());
    }

    /** @test */
    public function givenFilledInstanceWhenFillInstanceThenUpdatedOk(): void
    {
        $instanceArrayBuilder = VideoGamesArrayBuilder::withAttributes()
            ->addMetadata('custom-uuid', 'video-game-instance', [
                'status' => PublicationStatus::REVISION,
                'startPublishingDate' => '1989-03-08 09:00:00',
                'endPublishingDate' => '2021-07-27 14:30:00'
            ]);
        $instance = InstanceFactory::fill('VideoGames', static fn () => $instanceArrayBuilder->build());
        $instance->fill([
            'attributes' => ['synopsis' => ['values' => [
                ['uuid' => 'bc011d62-60a1-3d3c-8983-9126bfa4261b', 'language' => 'es', 'value' => 'sinopsis-editada'],
                ['uuid' => 'ec28da68-3b91-3aeb-9d44-f08778576426', 'language' => 'en', 'value' => 'synopsis-edited'],
            ]]],
            'metadata' => [],
            'relations' => [],
        ]);

        $jsonInstance = json_encode($instance->toArray(), JSON_THROW_ON_ERROR);
        $this->assertStringContainsString('custom-uuid', $jsonInstance);
        $this->assertStringContainsString('1989-03-08 09:00:00', $jsonInstance);
        $this->assertStringContainsString('sinopsis-editada', $jsonInstance);
        $this->assertStringContainsString('synopsis-edited', $jsonInstance);
    }

    /** @test */
    public function givenFilledInstanceWhenFillInstanceWithDifferentUuidThenUpdatedOk(): void
    {
        $instanceArrayBuilder = VideoGamesArrayBuilder::withAttributes()
            ->addMetadata('custom-uuid', 'video-game-instance', [
                'status' => PublicationStatus::REVISION,
                'startPublishingDate' => '1989-03-08 09:00:00',
                'endPublishingDate' => '2021-07-27 14:30:00'
            ]);
        $instance = InstanceFactory::fill('VideoGames', static fn () => $instanceArrayBuilder->build());
        $instance->fill([
            'attributes' => ['synopsis' => ['values' => [
                ['uuid' => 'bc011d62-60a1-3d3c-8983-9126bfa4261b', 'language' => 'es', 'value' => 'sinopsis-editada'],
                ['uuid' => 'ec28da68-3b91-3aeb-9d44-f08778576426', 'language' => 'en', 'value' => 'synopsis-edited'],
            ]]],
            'metadata' => [
                'uuid' => 'different-uuid'
            ],
            'relations' => [],
        ]);

        $jsonInstance = json_encode($instance->toArray(), JSON_THROW_ON_ERROR);
        $this->assertStringContainsString('different-uuid', $jsonInstance);
        $this->assertStringContainsString('1989-03-08 09:00:00', $jsonInstance);
        $this->assertStringContainsString('sinopsis-editada', $jsonInstance);
        $this->assertStringContainsString('synopsis-edited', $jsonInstance);
    }
}
