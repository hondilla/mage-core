<?php

namespace Tests\Editora\Data\Objects;

use Tests\Editora\Data\InstanceArrayBuilder;
use Tests\Editora\Data\InstanceFactory;

class NewsMother
{
    public static function get(int $instancesNumber = 1, array $relations = []): array
    {
        $instances = [];
        for($i=1; $i <= $instancesNumber; $i++) {
            $instances[] = InstanceFactory::fill('News', static function (InstanceArrayBuilder $builder) use ($relations, $i) {
                $builder
                    ->addClassKey('news')
                    ->addMetadata('uuid', 'new-instance-'.$i)
                    ->addAttribute('title', 'string', [
                        ['uuid' => 'uuid', 'language' => 'es', 'value' => 'title-es-'.$i],
                        ['uuid' => 'uuid', 'language' => 'en', 'value' => 'title-en-'.$i],
                        ['uuid' => 'uuid', 'language' => '+', 'value' => 'title-default-'.$i],
                    ])
                    ->addAttribute('description', 'text', [
                        ['uuid' => 'uuid', 'language' => 'es', 'value' => 'description-es-'.$i],
                        ['uuid' => 'uuid', 'language' => 'en', 'value' => 'description-en-'.$i],
                        ['uuid' => 'uuid', 'language' => '+', 'value' => 'description-default-'.$i],
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