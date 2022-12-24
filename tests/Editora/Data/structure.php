<?php declare(strict_types=1);

use Omatech\MageCore\Editora\Domain\Value\Types\StringValue;
use Tests\Editora\Data\UniqueValueRepository;

return [
    'classes' => [
        'News' => [
            'attributes' => [
                'title' => [
                    'values' => [
                        'languages' => [
                            '+' => [],
                        ],
                    ],
                ],
                'description' => null
            ],
            'relations' => [
                'NewsPhotos' => [
                    'Photos',
                ],
            ],
        ],
        'Articles' => [
            'attributes' => [
                'title' => null,
                'author' => null,
                'page' => null,
            ],
        ],
        'Pictures' => [
            'attributes' => [
                'url' => null,
            ],
        ],
        'Photos' => [
            'attributes' => [
                'url' => null,
            ],
            'relations' => [
                'PhotosLocations' => [
                    'Locations',
                    'Coordinates',
                ],
            ],
        ],
        'Locations' => [
            'attributes' => [
                'country' => null,
            ],
        ],
        'Coordinates' => [
            'attributes' => [
                'latitude' => null,
                'longitude' => null,
            ],
        ],
        'Books' => [
            'attributes' => [
                'title' => null,
                'isbn' => [
                    'values' => [
                        'languages' => [
                            '+' => [],
                        ],
                    ],
                ],
                'synopsis' => [
                    'values' => [
                        'languages' => [
                            '+' => [],
                        ],
                    ],
                ],
                'picture' => [
                    'attributes' => [
                        'alt' => null,
                        'location' => null
                    ],
                ],
                'price' => [
                    'values' => [
                        'languages' => [
                            '*' => [],
                        ],
                    ],
                ],
            ],
            'relations' => [
                'Articles' => [
                    'Articles',
                ],
                'Photos' => [
                    'Photos',
                    'Pictures',
                ],
            ],
        ],
        'VideoGames' => [
            'attributes' => [
                'Title' => [
                    'values' => [
                        'type' => 'Value',
                    ],
                    'attributes' => [
                        'Code' => [
                            'values' => [
                                'type' => 'Value',
                            ],
                        ],
                        'SubTitle' => [
                            'values' => [
                                'rules' => [
                                    'required' => true,
                                ],
                                'type' => StringValue::class,
                            ],
                        ],
                    ],
                ],
                'SubTitle' => [
                    'values' => [
                        'rules' => [
                            'uniqueDB' => [
                                'class' => UniqueValueRepository::class,
                            ],
                        ],
                        'type' => StringValue::class,
                    ],
                ],
                'Synopsis' => [
                    'type' => 'text',
                    'values' => [
                        'type' => 'JsonValue',
                        'rules' => [
                            'required' => true,
                        ],
                        'configuration' => [
                            'cols' => 10,
                            'rows' => 10,
                        ],
                    ],
                ],
                'ReleaseDate' => [
                    'values' => [
                        'type' => 'DateValue',
                        'rules' => [
                            'required' => true,
                        ],
                        'configuration' => [
                            'cols' => 30,
                            'rows' => 30,
                        ],
                        'languages' => [
                            '+' => null,
                            'es' => [
                                'rules' => [
                                    'required' => true,
                                ],
                                'configuration' => [
                                    'cols' => 10,
                                    'rows' => 10,
                                ],
                            ],
                            'en' => [
                                'rules' => [
                                    'required' => false,
                                ],
                                'configuration' => [
                                    'cols' => 20,
                                    'rows' => 20,
                                ],
                            ],
                        ],
                    ],
                ],
                'Code' => [
                    'type' => 'lookup',
                    'values' => [
                        'languages' => [
                            '*' => [
                                'type' => 'LookupValue',
                                'rules' => [
                                    'required' => true,
                                    'unique' => [],
                                ],
                                'configuration' => [
                                    'options' => [
                                        'pc-code',
                                        'playstation-code',
                                        'xbox-code',
                                        'switch-code',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'relations' => [
                'Platforms' => [
                    'Platform',
                ],
                'Reviews' => [
                    'Articles',
                    'Blogs',
                ],
            ],
        ],
        'Movies' => [
            'attributes' => [
                'Title' => [
                    'values' => [
                        'rules' => [
                            'noValidRule' => true,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
