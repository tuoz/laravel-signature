<?php


namespace Hypocenter\LaravelSignature\Tests\Unit\Payload\Resolvers;


use Hypocenter\LaravelSignature\Payload\Resolvers\HeaderResolver;
use Hypocenter\LaravelSignature\Payload\Resolvers\RequestProxy;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class HeaderResolverTest extends TestCase
{
    use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function testSetConfig(): void
    {
        $config = [
            'key_app_id'    => 'app-id',
            'key_sign'      => 'sign',
            'key_timestamp' => 'x-sign-time',
            'key_nonce'     => 'x-sign-nonce',
        ];

        $req = m::mock(RequestProxy::class);
        $rs = new HeaderResolver($req);
        $rs->setConfig($config);

        $mergedConf = (function () { return $this->config; })->call($rs);

        $this->assertEquals($config, $mergedConf);
    }

    public function testResolve(): void
    {
        $appId = 'app';
        $sign = 'sign';
        $nonce = 'nonce';
        $timestamp = time();
        $method = 'post';
        $path = '/path';
        $data = ['data' => 1];

        $req = m::mock(RequestProxy::class);
        $req->shouldReceive('method')->once()->andReturn($method);
        $req->shouldReceive('path')->once()->andReturn($path);
        $req->shouldReceive('all')->once()->andReturn($data);
        $req->shouldReceive('header')->with('X-SIGN-APP-ID')->andReturn($appId);
        $req->shouldReceive('header')->with('X-SIGN')->andReturn($sign);
        $req->shouldReceive('header')->with('X-SIGN-TIME')->andReturn($timestamp);
        $req->shouldReceive('header')->with('X-SIGN-NONCE')->andReturn($nonce);

        $rs = new HeaderResolver($req);

        $py = $rs->resolvePayload();

        $this->assertEquals($appId, $py->getAppId());
        $this->assertEquals($sign, $py->getSign());
        $this->assertEquals($nonce, $py->getNonce());
        $this->assertEquals($timestamp, $py->getTimestamp());
        $this->assertEquals($method, $py->getMethod());
        $this->assertEquals($path, $py->getPath());
        $this->assertEquals($data, $py->getData());
    }
}