<?php

namespace Tests\Editora\Data\Objects;

use Tests\Editora\Data\InstanceArrayBuilder;
use Tests\Editora\Data\InstanceFactory;

class BooksMother
{
    public static function get(int $instancesNumber = 1, array $relations = []): array
    {
        $instances = [];
        for($i=1; $i <= $instancesNumber; $i++) {
            $instances[] = InstanceFactory::fill('Books', static function (InstanceArrayBuilder $builder) use ($relations, $i) {
                $builder
                    ->addMetadata('uuid', 'book-instance-'.$i)
                    ->addAttribute('title', 'string', [
                        ['uuid' => 'uuid', 'language' => 'es', 'value' => 'title-es-'.$i],
                        ['uuid' => 'uuid', 'language' => 'en', 'value' => 'title-en-'.$i],
                    ])
                    ->addAttribute('isbn', 'text', [
                        ['uuid' => 'uuid', 'language' => 'es', 'value' => 'isbn-es-'.$i],
                        ['uuid' => 'uuid', 'language' => 'en', 'value' => 'isbn-en-'.$i],
                        ['uuid' => 'uuid', 'language' => '+', 'value' => 'isbn-en-'.$i],
                    ])
                    ->addAttribute('synopsis', 'text', [
                        ['uuid' => 'uuid', 'language' => 'es', 'value' => 'synopsis-es-'.$i],
                        ['uuid' => 'uuid', 'language' => 'en', 'value' => 'synopsis-en-'.$i],
                        ['uuid' => 'uuid', 'language' => '+', 'value' => 'synopsis-en-'.$i],
                    ])
                    ->addAttribute('picture', 'text', [
                        ['uuid' => 'uuid', 'language' => 'es', 'value' => 'picture-es-'.$i],
                        ['uuid' => 'uuid', 'language' => 'en', 'value' => 'picture-en-'.$i],
                        ['uuid' => 'uuid', 'language' => '+', 'value' => 'picture-en-'.$i],
                    ], [
                        fn (InstanceArrayBuilder $builder) => $builder->addSubAttribute('alt', 'string', [
                            ['uuid' => 'uuid', 'language' => 'es', 'value' => 'alt-es-'.$i],
                            ['uuid' => 'uuid', 'language' => 'en', 'value' => 'alt-en-'.$i],
                        ]),
                        fn (InstanceArrayBuilder $builder) => $builder->addSubAttribute('location', 'string', [
                            ['uuid' => 'uuid', 'language' => 'es', 'value' => 'location-es-'.$i],
                            ['uuid' => 'uuid', 'language' => 'en', 'value' => 'location-en-'.$i],
                        ]),
                    ])
                    ->addAttribute('price', 'text', [
                        ['uuid' => 'uuid', 'language' => '*', 'value' => 'price-'.$i],
                    ]);
                foreach($relations as $relation => $relatedInstances) {
                    $relatedInstancesIds = [];
                    foreach($relatedInstances as $relatedInstance) {
                        $relatedInstancesIds[$relatedInstance->uuid()] = $relatedInstance->data()['classKey'];
                    }
                    $builder->addRelation($relation, $relatedInstancesIds);
                }
                return $builder->build();
            });
        }
        return $instances;
    }
}
