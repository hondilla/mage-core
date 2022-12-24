<?php

namespace Data\Objects;

use Tests\Editora\Data\InstanceArrayBuilder;
use Tests\Editora\Data\InstanceFactory;

abstract class AbstractMother
{
    public static function get(int $instancesNumber, InstanceArrayBuilder $builder, array $relations = []): array
    {
        $instances = [];
        for($i=1; $i <= $instancesNumber; $i++) {
            $instances[] = InstanceFactory::fill('Photos', static function () use ($relations, $i, $builder) {
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