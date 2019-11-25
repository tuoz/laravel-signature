# laravel-signature
Signature helper for Laravel

[三方接入文档](./INTERGRATION.md)

#### 特性

* 对请求参数进行签名验证, 以保证数据的完整性
* 每次签名只能使用一次
* 支持 Laravel 5.x 和 Laravel 6.x

#### 安装

```bash
composer require hypocenter/laravel-signature
```

#### 配置

```
php artisan vendor:publish --provider="Hypocenter\LaravelSignature\SignatureServiceProvider"
```

执行命令后会生成配置文件 app/config/signature.php

```php
<?php

return [
    // 默认的驱动
    'default' => 'default',

    // 驱动配置
    'drivers' => [
        'default' => [
            'class'          => \Hypocenter\LaravelSignature\Signature::class,
            'resolver'       => 'header',
            'repository'     => 'array',
            'nonce_length'   => 16, // 随机字符串长度
            'time_tolerance' => 5 * 60, // 时间宽容度,秒
            'cache_driver'   => 'file', // 缓存驱动
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
                // Add more defines here.
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
```

#### 驱动

可以配置多个驱动以应对不同场景的应用配置

驱动需要使用下面配置的`Repository`和`Resolver`

#### Repository

定义如何获取应用配置

* ArrayRepository: 应用AppID和Secret配置在PHP数组中, 适合简单固定的使用场景
* ModelRepository: 应用AppID和Secret配置在数据库中,适合App较多的使用场景, 默认提供`Partner`模型来处理数据库操作. 可继承 Partner 类, 自定义模型

#### Resolver

定义如何从请求中获取相关校验参数

* HeaderResolver: 从 HTTP Header 中获取

#### 签名

如果作为客户端,单独使用签名可无需 `Resolver`, 但 `Repositroy` 必须配置

```php
$client = new \GuzzleHttp\Client(['base_uri' => env('RPC_SERVER')]);

$payload = new Payload();
$payload->setAppId('you app ID');
$payload->setData(['page' => 1, 'page_size' => 20]);
$payload->setMethod('GET');
$payload->setPath('api/users');

$driver = app('signature')->driver();
$driver->sign($payload);

$res = $client->request($payload->getMethod(), $payload->getPath() . '?'. http_build_query($payload->getData()), [
    'headers' => [
        'Accept'        =>"application/json",
        'X-SIGN-APP-ID' => $payload->getAppId(),
        'X-SIGN'        => $payload->getSign(),
        'X-SIGN-TIME'   => $payload->getTimestamp(),
        'X-SIGN-NONCE'  => $payload->getNonce()
    ]
]);
```

#### 中间件

配置

```php
class Kernel extends HttpKernel {
  protected $routeMiddleware = [
        // ...
        'signature' => \Hypocenter\LaravelSignature\Middleware\SignatureMiddleware::class
    ];
}
```

使用

```php
Route::get('test-sign', 'SignController')->middleware('signature'); // 使用默认渠道
Route::get('test-sign', 'SignController')->middleware('signature:custom'); // 使用其他驱动
```

