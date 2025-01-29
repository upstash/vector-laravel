<?php

return [
    'default' => env('UPSTASH_VECTOR_CONNECTION', 'default'),

    'connections' => [
        'default' => [
            'url' => env('UPSTASH_VECTOR_REST_URL'),
            'token' => env('UPSTASH_VECTOR_REST_TOKEN'),
        ],
    ],
];
