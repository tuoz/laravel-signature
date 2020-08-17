<?php

namespace Hypocenter\LaravelSignature\Tests\Feature;

use Hypocenter\LaravelSignature\Contracts\Factory;
use Hypocenter\LaravelSignature\Payload\Payload;
use Illuminate\Contracts\Routing\Registrar;


class DefaultSignatureTest extends SignatureTestCase
{
    public function testDefaultSignAndVerify(): void
    {
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
            'Accept'        => 'application/json',
            'X-SIGN-APP-ID' => $py->getAppId(),
            'X-SIGN'        => $py->getSign(),
            'X-SIGN-TIME'   => $py->getTimestamp(),
            'X-SIGN-NONCE'  => $py->getNonce(),
        ]);

        $res->assertOk();
        $res->assertSee('bar');

        $res = $this->post('/default/foo', ['a' => 1], [
            'Accept'        => 'application/json',
            'X-SIGN-APP-ID' => $py->getAppId(),
            'X-SIGN'        => $py->getSign(),
            'X-SIGN-TIME'   => $py->getTimestamp(),
            'X-SIGN-NONCE'  => $py->getNonce(),
        ]);

        $res->assertStatus(400);
        $res->assertSee('The signature has expired');
    }

    public function testGetRootPath(): void
    {
        $router = $this->app->make(Registrar::class);
        $router->get('/', static function () {
            return 'bar';
        });

        $py = Payload::forSign()
            ->setMethod('GET')
            ->setPath('/')
            ->build();

        $res = $this->get('/', [
            'Accept'        => 'application/json',
            'X-SIGN-APP-ID' => $py->getAppId(),
            'X-SIGN'        => $py->getSign(),
            'X-SIGN-TIME'   => $py->getTimestamp(),
            'X-SIGN-NONCE'  => $py->getNonce(),
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