<?php

namespace Tests\Editora\Domain\Extraction;

use Mockery;
use Mockery\MockInterface;
use Omatech\MageCore\Editora\Domain\Extraction\Contracts\ExtractionInterface;
use Omatech\MageCore\Editora\Domain\Extraction\ExtractionBuilder;
use Omatech\MageCore\Editora\Domain\Extraction\Pagination;
use Omatech\MageCore\Editora\Domain\Extraction\Parser;
use Omatech\MageCore\Editora\Domain\Extraction\Results;
use Tests\Editora\Data\Objects\ArticleMother;
use Tests\Editora\Data\Objects\BooksMother;
use Tests\Editora\Data\Objects\LocationMother;
use Tests\Editora\Data\Objects\NewsMother;
use Tests\Editora\Data\Objects\PhotosMother;
use Tests\TestCase;

class InstanceExtractionTest extends TestCase
{
    /** @test */
    public function given(): void
    {
        $graphQuery = '{
            News(preview: false, languages: [es, en], page: 1)
        }';

        $mock = $this->mockExtraction($graphQuery, [
            ['instances' => []],
        ]);

        $extraction = (new ExtractionBuilder($mock))
            ->setQuery($graphQuery)
            ->build();

        $this->assertEquals([
            'total' => 0,
            'limit' => 0,
            'current' => 1,
            'pages' => 0
        ], $extraction->results()[0]->pagination()->toArray());

        $this->assertSame([], $extraction->toArray());
    }

    /** @test */
    public function givenMultiLanguageQueryWhenExtractedThenOk(): void
    {
        $news = NewsMother::get(2);

        $graphQuery = '{
            News(preview: false, languages: [es, en], limit: 5, page: 1)
        }';

        $mock = $this->mockExtraction($graphQuery, [
            ['instances' => $news],
        ]);

        $extraction = (new ExtractionBuilder($mock))
            ->setQuery($graphQuery)
            ->build()
            ->toArray();

        $this->assertEquals([
            'news' => [
                ['key' => 'new-instance-1', 'attributes' => [
                    'es' => [
                        'title' => ['uuid' => 'uuid', 'value' => 'title-es-1', 'attributes' => []],
                        'description' => ['uuid' => 'uuid', 'value' => 'description-es-1', 'attributes' => []],
                    ],
                    'en' => [
                        'title' => ['uuid' => 'uuid', 'value' => 'title-en-1', 'attributes' => []],
                        'description' => ['uuid' => 'uuid', 'value' => 'description-en-1', 'attributes' => []],
                    ]
                ], 'relations' => []],
                ['key' => 'new-instance-2', 'attributes' => [
                    'es' => [
                        'title' => ['uuid' => 'uuid', 'value' => 'title-es-2', 'attributes' => []],
                        'description' => ['uuid' => 'uuid', 'value' => 'description-es-2', 'attributes' => []],
                    ],
                    'en' => [
                        'title' => ['uuid' => 'uuid', 'value' => 'title-en-2', 'attributes' => []],
                        'description' => ['uuid' => 'uuid', 'value' => 'description-en-2', 'attributes' => []],
                    ]
                ], 'relations' => []]
            ]
        ], $extraction);
    }

    /** @test */
    public function givenPaginateQueryWhenExtractedThenOk(): void
    {
        $photos = PhotosMother::get(1);
        $news = NewsMother::get(11, [ 'news-photos' => $photos ]);

        $graphQuery = '{
            News(languages: es, limit: 5, page: 1) {
                NewsPhotos()
            }
        }';

        $mock = $this->mockExtraction($graphQuery, [[
            'instances' => $news,
            'relations' => [
                'instances' => [$photos]
            ]
        ]]);

        $extraction = (new ExtractionBuilder($mock))
            ->setQuery($graphQuery)
            ->build();

        $this->assertEquals([
            'total' => 11,
            'limit' => 5,
            'current' => 1,
            'pages' => 3
        ], $extraction->results()[0]->pagination()->toArray());
        $this->assertEquals(5, $extraction->results()[0]->pagination()->limit());
        $this->assertEquals(0, $extraction->results()[0]->pagination()->offset());

        $this->assertFalse($extraction->results()[0]->params()['preview']);
        $this->assertEquals([
            'total' => 1,
            'limit' => 1,
            'current' => 1,
            'pages' => 1
        ], $extraction->results()[0]->relations()[0]->pagination()->toArray());
        $this->assertEquals(0, $extraction->results()[0]->relations()[0]->pagination()->offset());
        $this->assertEquals($graphQuery, $extraction->query());
    }

    /** @test */
    public function givenMultiClassQueryWhenExtractedThenOk(): void
    {
        $news = NewsMother::get(2);
        $articles = ArticleMother::get(2);

        $graphQuery = '{
            News(preview: false, languages: [es], limit: 5, page: 1)
            Articles(preview: false, languages: [es])
        }';

        $mock = $this->mockExtraction($graphQuery, [
            ['instances' => $news],
            ['instances' => $articles],
        ]);

        $extraction = (new ExtractionBuilder($mock))
            ->setQuery($graphQuery)
            ->build()
            ->toArray();

        $expected = [
            'news' => [
                ['key' => 'new-instance-1', 'attributes' => [ 'es' => [
                    'title' => ['uuid' => 'uuid', 'value' => 'title-es-1', 'attributes' => []],
                    'description' => ['uuid' => 'uuid', 'value' => 'description-es-1', 'attributes' => []],
                ]], 'relations' => []],
                ['key' => 'new-instance-2', 'attributes' => [ 'es' => [
                    'title' => ['uuid' => 'uuid', 'value' => 'title-es-2', 'attributes' => []],
                    'description' => ['uuid' => 'uuid', 'value' => 'description-es-2', 'attributes' => []],
                ]], 'relations' => []]
            ],
            'articles' => [
                ['key' => 'article-instance-1', 'attributes' => [ 'es' => [
                    'title' => ['uuid' => 'uuid', 'value' => 'title-es-1', 'attributes' => []],
                    'author' => ['uuid' => 'uuid', 'value' => 'author-es-1', 'attributes' => []],
                    'page' => ['uuid' => 'uuid', 'value' => 'page-es-1', 'attributes' => []],
                ]], 'relations' => []],
                ['key' => 'article-instance-2', 'attributes' => [ 'es' => [
                    'title' => ['uuid' => 'uuid', 'value' => 'title-es-2', 'attributes' => []],
                    'author' => ['uuid' => 'uuid', 'value' => 'author-es-2', 'attributes' => []],
                    'page' => ['uuid' => 'uuid', 'value' => 'page-es-2', 'attributes' => []],
                ]], 'relations' => []],
            ]
        ];

        $this->assertEquals($expected, $extraction);
    }

    /** @test */
    public function givenInstanceQueryWhenExtractedThenOk(): void
    {
        $news = NewsMother::get(1);
        $articles = ArticleMother::get(1);

        $graphQuery = '{
            instances(key: "new-instance-1", languages: [es])
            instances(key: "article-instance-1", languages: [es], preview: true) {
                title
                author
            }
        }';

        $mock = $this->mockExtraction($graphQuery, [
            ['instances' => $news],
            ['instances' => $articles]
        ]);

        $extraction = (new ExtractionBuilder($mock))
            ->setQuery($graphQuery)
            ->build()
            ->toArray();

        $expected = [
            'news' => [
                ['key' => 'new-instance-1', 'attributes' => [ 'es' => [
                    'title' => ['uuid' => 'uuid', 'value' => 'title-es-1', 'attributes' => []],
                    'description' => ['uuid' => 'uuid', 'value' => 'description-es-1', 'attributes' => []],
                ]], 'relations' => []],
            ],
            'articles' => [
                ['key' => 'article-instance-1', 'attributes' => [ 'es' => [
                    'title' => ['uuid' => 'uuid', 'value' => 'title-es-1', 'attributes' => []],
                    'author' => ['uuid' => 'uuid', 'value' => 'author-es-1', 'attributes' => []],
                ]], 'relations' => []],
            ],
        ];
        $this->assertEquals($expected, $extraction);
    }

    /** @test */
    public function givenMultiRelationQueryWhenExtractedThenOk(): void
    {
        $locations = LocationMother::get(1);
        $photos = PhotosMother::get(1, [ 'photos-locations' => $locations ]);
        $news = NewsMother::get(1, [ 'news-photos' => $photos ]);

        $graphQuery = '{
            News(languages: es) {
                title
                NewsPhotos(limit:7, page: 2) {
                    PhotosLocations(limit: 2) {
                        country
                    }
                }
            }
        }';

        $mock = $this->mockExtraction($graphQuery, [[
            'instances' => $news,
            'relations' => [
                'instances' => [$photos],
                'relations' => [
                    'instances' => [$locations]
                ]
            ]
        ]]);

        $extraction = (new ExtractionBuilder($mock))
            ->setQuery($graphQuery)
            ->build()
            ->toArray();

        $expected = [
            'news' => [
                ['key' => 'new-instance-1', 'attributes' => [ 'es' => [
                    'title' => ['uuid' => 'uuid', 'value' => 'title-es-1', 'attributes' => []],
                ]], 'relations' => [ 'news-photos' => [ 'child' => [
                    ['key' => 'photo-instance-1', 'attributes' => [ 'es' => [
                        'url' => ['uuid' => 'uuid', 'value' => 'url-es-1', 'attributes' => []],
                    ]], 'relations' => [ 'photos-locations' => [ 'child' => [
                        ['key' => 'location-instance-1', 'attributes' => [ 'es' => [
                            'country' => ['uuid' => 'uuid', 'value' => 'country-es-1', 'attributes' => []]
                        ]], 'relations' => []]
                    ]]]]
                ]]]]
            ],
        ];
        $this->assertEquals($expected, $extraction);
    }

    /** @test */
    public function givenComplexQueryWhenExtractedThenOk(): void
    {
        $locations = LocationMother::get(1);
        $photos = PhotosMother::get(3, [ 'photos-locations' => $locations ]);
        $articles = ArticleMother::get(1);
        $books = BooksMother::get(1, [ 'articles' => $articles, 'photos' => $photos ]);

        $graphQuery = '{
            Books(languages: [en]) {
                title,
                isbn,
                synopsis,
                picture {
                    alt,
                    location
                }
                price
                Articles(limit: 1)
                Photos(limit: 3) {
                    PhotosLocations(limit: 1)
                }
            }
        }';

        $mock = $this->mockExtraction($graphQuery, [[
            'instances' => $books,
            'relations' => [
                'instances' => [$articles,  $photos],
                'relations' => [
                    'instances' => [$locations]
                ]
            ]
        ]]);

        $extraction = (new ExtractionBuilder($mock))
            ->setQuery($graphQuery)
            ->build()
            ->toArray();

        $expected = [
            'books' => [
                ['key' => 'book-instance-1', 'attributes' => [ 'en' => [
                    'title' => ['uuid' => 'uuid', 'value' => 'title-en-1', 'attributes' => []],
                    'isbn' => ['uuid' => 'uuid', 'value' => 'isbn-en-1', 'attributes' => []],
                    'synopsis' => ['uuid' => 'uuid', 'value' => 'synopsis-en-1', 'attributes' => []],
                    'picture' => ['uuid' => 'uuid', 'value' => 'picture-en-1', 'attributes' => [
                        'alt' => ['uuid' => 'uuid', 'value' => 'alt-en-1', 'attributes' => []],
                        'location' => ['uuid' => 'uuid', 'value' => 'location-en-1', 'attributes' => []],
                    ]],
                    'price' => ['uuid' => null, 'value' => 'price-1', 'attributes' => []],
                ]], 'relations' => [
                    'articles' => [ 'child' => [
                        ['key' => 'article-instance-1', 'attributes' => [ 'en' => [
                            'title' => ['uuid' => 'uuid', 'value' => 'title-en-1', 'attributes' => []],
                            'author' => ['uuid' => 'uuid', 'value' => 'author-en-1', 'attributes' => []],
                            'page' => ['uuid' => 'uuid', 'value' => 'page-en-1', 'attributes' => []],
                    ]], 'relations' => []]]],
                    'photos' => [ 'child' => [
                        ['key' => 'photo-instance-1', 'attributes' => [ 'en' => [
                            'url' => ['uuid' => 'uuid', 'value' => 'url-en-1', 'attributes' => []],
                        ]], 'relations' => [ 'photos-locations' => [ 'child' => [
                            ['key' => 'location-instance-1', 'attributes' => [ 'en' => [
                                'country' => ['uuid' => 'uuid', 'value' => 'country-en-1', 'attributes' => []]
                            ]], 'relations' => []]
                        ]]]],
                        ['key' => 'photo-instance-2', 'attributes' => [ 'en' => [
                            'url' => ['uuid' => 'uuid', 'value' => 'url-en-2', 'attributes' => []],
                        ]], 'relations' => [ 'photos-locations' => [ 'child' => [
                            ['key' => 'location-instance-1', 'attributes' => [ 'en' => [
                                'country' => ['uuid' => 'uuid', 'value' => 'country-en-1', 'attributes' => []]
                            ]], 'relations' => []]
                        ]]]],
                        ['key' => 'photo-instance-3', 'attributes' => [ 'en' => [
                            'url' => ['uuid' => 'uuid', 'value' => 'url-en-3', 'attributes' => []],
                        ]], 'relations' => [ 'photos-locations' => [ 'child' => [
                            ['key' => 'location-instance-1', 'attributes' => [ 'en' => [
                                'country' => ['uuid' => 'uuid', 'value' => 'country-en-1', 'attributes' => []]
                            ]], 'relations' => []]
                        ]]]],
                    ]]
                ]]
            ],
        ];
        $this->assertEquals($expected, $extraction);
    }

    private function mockExtraction(string $graphQuery, array $instances): ExtractionInterface
    {
        $mock = Mockery::mock(ExtractionInterface::class);
        $parsedQuery = (new Parser())->parse($graphQuery);
        foreach($parsedQuery as $index => $query) {
            $mock->shouldReceive('instancesBy')
                ->with($query->params())
                ->andReturn(new Results($instances[$index]['instances'], new Pagination([
                    'page' => $query->params()['page'],
                    'limit' => $query->params()['limit'],
                ], is_countable($instances[$index]['instances']) ? count($instances[$index]['instances']) : 0)))
                ->once();
            $this->mockRelations($mock, $query->relations(), $instances[$index]['relations'] ?? []);
        }
        return $mock;
    }

    private function mockRelations(MockInterface $mock, array $relations, $relatedInstances): void
    {
        foreach ($relations as $relation) {
            $instance = array_shift($relatedInstances['instances']);
            $mock->shouldReceive('findRelatedInstances')
                ->with('uuid', $relation->params())
                ->andReturn(new Results($instance, new Pagination([
                    'page' => $relation->params()['page'],
                    'limit' => $relation->params()['limit'],
                ], \is_countable($instance) ? count($instance) : 0)))
                ->atLeast();
            if($relation->relations()) {
                $this->mockRelations($mock, $relation->relations(), $relatedInstances['relations']);
            }
        }
    }
}
