<?php

namespace Tests\Editora\Data\Objects;

use Tests\Editora\Data\InstanceArrayBuilder;
use Tests\Editora\Data\InstanceFactory;

class ArticleMother
{
    public static function get(int $instancesNumber = 1): array
    {
        $instances = [];
        for($i=1; $i <= $instancesNumber; $i++) {
            $instances[] = InstanceFactory::fill('Articles', static fn(InstanceArrayBuilder $builder) => $builder
                ->addMetadata('uuid', 'article-instance-'.$i)
                ->addAttribute('title', 'string', [
                    ['uuid' => 'uuid', 'language' => 'es', 'value' => 'title-es-'.$i],
                    ['uuid' => 'uuid', 'language' => 'en', 'value' => 'title-en-'.$i],
                ])
                ->addAttribute('author', 'text', [
                    ['uuid' => 'uuid', 'language' => 'es', 'value' => 'author-es-'.$i],
                    ['uuid' => 'uuid', 'language' => 'en', 'value' => 'author-en-'.$i],
                ])
                ->addAttribute('page', 'text', [
                    ['uuid' => 'uuid', 'language' => 'es', 'value' => 'page-es-'.$i],
                    ['uuid' => 'uuid', 'language' => 'en', 'value' => 'page-en-'.$i],
                ])
                ->build());
        }
        return $instances;
    }
}