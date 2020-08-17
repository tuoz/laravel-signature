<?php


namespace Hypocenter\LaravelSignature\Tests\Unit\Payload\Resolvers;


use Hypocenter\LaravelSignature\Payload\Resolvers\QueryResolver;
use Hypocenter\LaravelSignature\Payload\Resolvers\RequestProxy;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class QueryResolverTest extends TestCase
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
        $rs = new QueryResolver($req);
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
        $data = ['data' => 1, '_appid' => 'appid'];

        $req = m::mock(RequestProxy::class);
        $req->shouldReceive('method')->once()->andReturn($method);
        $req->shouldReceive('path')->once()->andReturn($path);
        $req->shouldReceive('all')->once()->andReturn($data);
        $req->shouldReceive('get')->with('_appid')->andReturn($appId);
        $req->shouldReceive('get')->with('_sign')->andReturn($sign);
        $req->shouldReceive('get')->with('_time')->andReturn($timestamp);
        $req->shouldReceive('get')->with('_nonce')->andReturn($nonce);

        $rs = new QueryResolver($req);

        $py = $rs->resolvePayload();

        $this->assertEquals($appId, $py->getAppId());
        $this->assertEquals($sign, $py->getSign());
        $this->assertEquals($nonce, $py->getNonce());
        $this->assertEquals($timestamp, $py->getTimestamp());
        $this->assertEquals($method, $py->getMethod());
        $this->assertEquals($path, $py->getPath());
        $this->assertEquals(['data' => 1], $py->getData());
    }
}