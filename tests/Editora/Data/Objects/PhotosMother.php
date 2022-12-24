<?php

namespace Tests\Editora\Data\Objects;

use Tests\Editora\Data\InstanceArrayBuilder;
use Tests\Editora\Data\InstanceFactory;

class PhotosMother
{
    public static function get(int $instancesNumber, array $relations = []): array
    {
        $instances = [];
        for($i=1; $i <= $instancesNumber; $i++) {
            $instances[] = InstanceFactory::fill('Photos', static function (InstanceArrayBuilder $builder) use ($relations, $i) {
                $builder
                    ->addClassRelations('photos-locations', [ 'locations', 'coordinates' ])
                    ->addMetadata('uuid', 'photo-instance-'.$i)
                    ->addAttribute('url', 'string', [
                        ['uuid' => 'uuid', 'language' => 'es', 'value' => 'url-es-'.$i],
                        ['uuid' => 'uuid', 'language' => 'en', 'value' => 'url-en-'.$i]
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