<?php declare(strict_types=1);

namespace Tests\Editora\Domain\Extraction;

use Omatech\MageCore\Editora\Domain\Extraction\Parser;
use Tests\TestCase;

class InstanceExtractionQueryParserTest extends TestCase
{
    /** @test */
    public function givenExtractionExpressionWhenGetParsedThenGeneratedArrayIsOk(): void
    {
        $graphQuery = '{
            Books(languages: [en], preview: true) {
                title,
                isbn,
                synopsis,
                picture {
                    alt
                }
                price
                Articles(limit: 1, page: 2, type: parent)
                Photos(limit: 3, type: "invalid-type") {
                    Location()
                }
            }
        }';
        $query = (new Parser())->parse($graphQuery)[0];

        $this->assertNull($query->param('non-existent-param'));
        $this->assertSame([
            'languages' => ['en'],
            'attributes' => [
                [
                    'key' => 'title',
                    'attributes' => [],
                ], [
                    'key' => 'isbn',
                    'attributes' => [],
                ], [
                    'key' => 'synopsis',
                    'attributes' => [],
                ], [
                    'key' => 'picture',
                    'attributes' => [
                        [
                            'key' => 'alt',
                            'attributes' => [],
                        ],
                    ],
                ], [
                    'key' => 'price',
                    'attributes' => [],
                ],
            ],
            'params' => [
                'languages' => ['en'],
                'preview' => true,
                'class' => 'books',
                'key' => null,
                'limit' => 0,
                'page' => 1,
            ],
            'relations' => [
                [
                    'languages' => ['en'],
                    'attributes' => [],
                    'params' => [
                        'limit' => 1,
                        'page' => 2,
                        'type' => 'parent',
                        'key' => 'articles',
                        'class' => null,
                        'preview' => true,
                        'languages' => ['en'],
                    ],
                    'relations' => [],
                    'pagination' => null,
                ], [
                    'languages' => ['en'],
                    'attributes' => [],
                    'params' => [
                        'limit' => 3,
                        'type' => 'child',
                        'key' => 'photos',
                        'class' => null,
                        'preview' => true,
                        'page' => 1,
                        'languages' => ['en'],
                    ],
                    'relations' => [
                        [
                            'languages' => ['en'],
                            'attributes' => [],
                            'params' => [
                                'limit' => 0,
                                'key' => 'location',
                                'class' => null,
                                'preview' => true,
                                'page' => 1,
                                'languages' => ['en'],
                                'type' => 'child',
                            ],
                            'relations' => [],
                            'pagination' => null,
                        ],
                    ],
                    'pagination' => null,
                ],
            ],
            'pagination' => null,
        ], $query->toArray());
    }
}
