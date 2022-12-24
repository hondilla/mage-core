<?php

namespace Tests\Editora\Data;

class VideoGamesArrayBuilder
{
    public static function withAttributes(): InstanceArrayBuilder
    {
        return (new InstanceArrayBuilder)
            ->addAttribute('title', 'string', [
                ['uuid' => 'c342193b-1c16-3077-af26-84cf15acc9a2', 'language' => 'es', 'value' => 'titulo'],
                ['uuid' => 'f3078d87-e366-3506-9077-d24a872af11c', 'language' => 'en', 'value' => 'title'],
            ], [
                fn (InstanceArrayBuilder $builder) => $builder->addSubAttribute('code', 'string', [
                    ['uuid' => '40f91d9f-51c2-36a2-815c-922e20136bee', 'language' => 'es', 'value' => null],
                    ['uuid' => '818e45fc-6d6c-3352-a69b-37c1ebf720a2', 'language' => 'en', 'value' => null],
                ]),
                fn (InstanceArrayBuilder $builder) => $builder->addSubAttribute('sub-title', 'string', [
                    ['uuid' => '65b12889-81d8-3068-a1ca-202dcd3ee4a6', 'language' => 'es', 'value' => 'sub-sub-titulo'],
                    ['uuid' => '84968337-90d0-3412-b33f-5afd23e39c9f', 'language' => 'en', 'value' => 'sub-sub-title'],
                ]),
            ])->addAttribute('sub-title', 'string', [
                ['uuid' => '8b105764-6fb3-3f94-a2a2-5360064cab40', 'language' => 'es', 'value' => 'sub-titulo'],
                ['uuid' => '592f4da6-2243-34e0-af33-6c2b1a5ebabc', 'language' => 'en', 'value' => 'sub-title'],
            ])->addAttribute('synopsis', 'string', [
                ['uuid' => 'bc011d62-60a1-3d3c-8983-9126bfa4261b', 'language' => 'es', 'value' => 'sinopsis'],
                ['uuid' => 'ec28da68-3b91-3aeb-9d44-f08778576426', 'language' => 'en', 'value' => 'synopsis'],
            ])->addAttribute('release-date', 'string', [
                ['uuid' => 'fe49b765-9b45-3b68-9467-c4b4a5154946', 'language' => 'es', 'value' => 'fecha-salida'],
                ['uuid' => '798f58a9-df29-36f9-b4c8-3028c6ddcf2f', 'language' => 'en', 'value' => 'release-date'],
                ['uuid' => 'de17be73-260a-38ed-bef4-f7fdfae85201', 'language' => '+', 'value' => 'default-date'],
            ])->addAttribute('code', 'string', [
                ['uuid' => 'f6e5c12e-99f4-3d5e-b8a8-0fc702b9fda7', 'language' => '*', 'value' => 'playstation-code']
            ]);
    }
}