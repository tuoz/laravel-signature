<?php

namespace Hypocenter\LaravelSignature\Tests\Feature;

use Hypocenter\LaravelSignature\Contracts\Factory;
use Hypocenter\LaravelSignature\Payload\Payload;


class DefaultSignatureTest extends SignatureTestCase
{
    public function testDefaultSignAndVerify(): void
    {
        $this->withoutExceptionHandling();
        $config = $this->app->get('config')->get('signature');

        $this->setUpRoute();
        $py = Payload::forSign()
            ->setMethod('post')
            ->setPath('/default/foo')
            ->setData(['a' => 1])
            ->build();

        $ctx = $this->app->make(Factory::class)->get()->sign($py);

        $this->assertEquals(data_get($config, 'signatures.default.default_app_id'), $ctx->getDefine()->getId());
        $this->assertEquals(40, strlen($ctx->getSign()));

        $res = $this->post('/default/foo', ['a' => 1], [
            'X-SIGN-APP-ID' => $ctx->getPayload()->getAppId(),
            'X-SIGN'        => $ctx->getPayload()->getSign(),
            'X-SIGN-TIME'   => $ctx->getPayload()->getTimestamp(),
            'X-SIGN-NONCE'  => $ctx->getPayload()->getNonce(),
        ]);

        $res->assertOk();
        $res->assertSee('bar');
    }

    public function testQuerySignAndVerify(): void
    {
        $this->withoutExceptionHandling();

        $this->setUpCustomSignatureConfig([
            'resolver'       => 'query',
            'repository'     => 'array',
            'nonce_length'   => 16,
            'cache_driver'   => 'file',
            'cache_name'     => 'laravel-signature',
            'time_tolerance' => 5 * 60,
            'default_app_id' => 'tFVzAUy07VIj2p8v',
        ]);

        $this->setUpRoute('custom');
        $py = Payload::forSign()
            ->setMethod('post')
            ->setPath('/custom/foo')
            ->setData(['a' => 1])
            ->build();

        $ctx = $this->app->make(Factory::class)->get('custom')->sign($py);

        $params = http_build_query([
            '_appid' => $ctx->getPayload()->getAppId(),
            '_sign'  => $ctx->getPayload()->getSign(),
            '_time'  => $ctx->getPayload()->getTimestamp(),
            '_nonce' => $ctx->getPayload()->getNonce(),
        ]);

        $res = $this->post('/custom/foo?' . $params, ['a' => 1]);

        $res->assertOk();
        $res->assertSee('bar');
    }
}