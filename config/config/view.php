<?php

declare(strict_types=1);

return [

    'default' => env('VIEW_COMPUTED_PATH', realpath(storage_path('framework/views'))),

    'paths' => [
        resource_path('views'),
    ],

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];
