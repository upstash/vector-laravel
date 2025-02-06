<?php

return [

    /**
     * The default connection name to use for the vector client.
     */
    'default' => env('UPSTASH_VECTOR_CONNECTION', 'default'),

    /**
     * The list of connections to use for the vector client.
     */
    'connections' => [
        'default' => [
            'url' => env('UPSTASH_VECTOR_REST_URL'),
            'token' => env('UPSTASH_VECTOR_REST_TOKEN'),
        ],
    ],

];
