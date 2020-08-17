<?php

return [
    // 默认的驱动
    'default'      => 'default',

    // 支持多个签名器配置
    'signatures'   => [
        'default' => [
            'resolver'       => 'header',
            'repository'     => 'array',
            'nonce_length'   => 16,
            'cache_driver'   => 'file',
            'cache_name'     => 'laravel-signature',
            'time_tolerance' => 5* 60,
            'default_app_id' => 'tFVzAUy07VIj2p8v',
        ]
    ],

    // 数据获取器定义，支持从不同来源获取
    'resolvers'    => [
        'header' => [
            'class'         => Hypocenter\LaravelSignature\Payload\Resolvers\HeaderResolver::class,
            'key_app_id'    => 'X-SIGN-APP-ID',
            'key_sign'      => 'X-SIGN',
            'key_timestamp' => 'X-SIGN-TIME',
            'key_nonce'     => 'X-SIGN-NONCE',
        ],
        'query'  => [
            'class'         => Hypocenter\LaravelSignature\Payload\Resolvers\QueryResolver::class,
            'key_app_id'    => '_appid',
            'key_sign'      => '_sign',
            'key_timestamp' => '_time',
            'key_nonce'     => '_nonce',
        ]
    ],

    // App 定义数据仓库，支持从不同来源获取
    'repositories' => [
        // 从数据库中读取
        'model' => [
            'class' => Hypocenter\LaravelSignature\Define\Repositories\ModelRepository::class,
            'model' => Hypocenter\LaravelSignature\Define\Models\AppDefine::class,
        ],
        // 从配置文件中读取
        'array' => [
            'class'   => Hypocenter\LaravelSignature\Define\Repositories\ArrayRepository::class,
            'defines' => [
                // Add more defines here.
                [
                    'id'     => 'tFVzAUy07VIj2p8v',
                    'name'   => 'RPC',
                    'secret' => 'u4JsCDCwCUakBCVn',
                    'config' => null
                ],
            ],
        ],
    ],
];