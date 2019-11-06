<?php

return [
    'default' => env('SIGNATURE_DRIVER', 'default'),

    'drivers' => [
        'default' => [
            'class'        => \Hypocenter\LaravelSignature\Signature::class,
            'resolver'     => 'header',
            'repository'   => 'array',
            'nonce_length' => 16,
            'ttl'          => 5 * 60,
            'cache_driver' => 'default',
        ]
    ],

    'resolvers' => [
        'header' => [
            'class'         => \Hypocenter\LaravelSignature\Resolvers\HeaderResolver::class,
            'key_app_id'    => 'X-SIGN-APP-ID',
            'key_sign'      => 'X-SIGN',
            'key_timestamp' => 'X-SIGN-TIME',
            'key_nonce'     => 'X-SIGN-NONCE',
        ]
    ],

    'repositories' => [
        'model' => [
            'class' => \Hypocenter\LaravelSignature\Repositories\ModelRepository::class,
            'model' => \Hypocenter\LaravelSignature\Models\Partner::class,
        ],

        'array' => [
            'class'   => \Hypocenter\LaravelSignature\Repositories\ArrayRepository::class,
            'defines' => [
                [
                    'id'     => 'tFVzAUy07VIj2p8v',
                    'name'   => 'RPC',
                    'secret' => 'u4JsCDCwCUakBCVn',
                    'config' => null
                ],
            ],
        ]
    ],
];