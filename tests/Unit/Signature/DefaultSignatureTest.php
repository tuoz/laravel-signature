<?php


namespace Hypocenter\LaravelSignature\Tests\Unit\Signature;


use Hypocenter\LaravelSignature\Define\Define;
use Hypocenter\LaravelSignature\Define\Repository;
use Hypocenter\LaravelSignature\Exceptions\InvalidArgumentException;
use Hypocenter\LaravelSignature\Exceptions\VerifyException;
use Hypocenter\LaravelSignature\Payload\Payload;
use Hypocenter\LaravelSignature\Payload\Resolver;
use Hypocenter\LaravelSignature\Signature\DefaultSignature;
use Illuminate\Contracts\Cache\Factory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class DefaultSignatureTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testSetter(): void
    {
        $sign = new DefaultSignature();
        $config = [
            'nonce_length'   => 10,
            'cache_driver'   => 'xcache',
            'cache_name'     => 'laravel-signature',
            'time_tolerance' => 5 * 600,
            'default_app_id' => 'xxxxxxx',
        ];

        $sign->setConfig($config);

        $res = (function () {
            return [
                'nonce_length'   => $this->nonceLength,
                'cache_driver'   => $this->cacheDriver,
                'cache_name'     => $this->cacheName,
                'time_tolerance' => $this->timeTolerance,
                'default_app_id' => $this->defaultAppId,
            ];
        })->call($sign);

        $this->assertEquals($config, $res);

        $rp = m::spy(Repository::class);
        $rs = m::spy(Resolver::class);

        $sign->setRepository($rp);
        $sign->setResolver($rs);

        $res = (function () {
            return [$this->repository, $this->resolver];
        })->call($sign);

        $this->assertEquals([$rp, $rs], $res);
    }

    public function testResolve(): void
    {
        $py = m::spy(Payload::class);
        $rs = m::spy(Resolver::class);
        $rs->shouldReceive('resolvePayload')->once()->andReturn($py);
        $sign = new DefaultSignature();
        $sign->setResolver($rs);

        $this->assertEquals($py, $sign->resolve());
    }

    public function testSignAndVerify(): void
    {
        $time = time();
        $appID = '321';
        $secret = 'xxxx';
        $path = '/path';
        $data = ['awesome'];
        $method = 'post';

        $py = Payload::forSign()
            ->setAppId($appID)
            ->setTimestamp($time)
            ->setAppId($appID)
            ->setPath($path)
            ->setMethod($method)
            ->setData($data)
            ->build();

        $def = new Define($appID, 'test app', $secret, []);

        $rp = m::mock(Repository::class);
        $rp->shouldReceive('findByAppId')->twice()->with($appID)->andReturn($def);
        $rp->shouldReceive('findByAppId')->with('xxxxx')->andReturn(null);

        $sign = new DefaultSignature();
        $sign->setRepository($rp);
        $ctx = $sign->sign($py);

        $this->assertEquals($py, $ctx->getPayload());
        $this->assertEquals($def, $ctx->getDefine());
        $this->assertEquals(40, strlen($ctx->getSign()));

        $this->assertEquals($appID, $py->getAppId());
        $this->assertEquals($data, $py->getData());
        $this->assertIsNumeric($py->getTimestamp());
        $this->assertEquals(16, strlen($py->getNonce()));
        $this->assertEquals($ctx->getSign(), $py->getSign());
        $this->assertEquals($path, $py->getPath());
        $this->assertEquals($method, $py->getMethod());

        $py2ver = Payload::forVerify()
            ->setAppId($appID)
            ->setSign($ctx->getSign())
            ->setMethod($method)
            ->setPath($path)
            ->setData($data)
            ->setTimestamp($py->getTimestamp())
            ->setNonce($py->getNonce())
            ->build();

        $cs = m::mock(\Illuminate\Contracts\Cache\Repository::class);
        $cs->shouldReceive('get')->once()->with('laravel_signature:' . $py2ver->getSign())->andReturn(null);
        $cs->shouldReceive('set')->once()->with('laravel_signature:' . $py2ver->getSign(), 1, 601);
        $cf = m::mock(Factory::class);
        $cf->shouldReceive('store')->twice()->andReturn($cs);

        $sign = new DefaultSignature($cf);
        $sign->setConfig(['cache_driver' => 'fake']);
        $sign->setRepository($rp);

        $ctx = $sign->verify($py2ver);
        $this->assertEquals($py2ver, $ctx->getPayload());
        $this->assertEquals($def, $ctx->getDefine());
        $this->assertEquals($py2ver->getSign(), $ctx->getSign());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('app define "xxxxx" not found');
        $py->setAppId('xxxxx');
        $sign->sign($py);
    }

    public function testSignException(): void
    {
        // TODO(hypo)
        $this->assertEmpty(null);
    }

    public function testVerifyExceptionInvalidSign(): void
    {
        $py = Payload::forVerify()
            ->setAppId('123')
            ->setPath('/path')
            ->setMethod('post')
            ->setNonce('1111')
            ->setTimestamp(time() - 5 * 60 - 1)
            ->setSign('xxxxx')
            ->build();

        $sign = new DefaultSignature();

        $this->expectException(VerifyException::class);
        $this->expectExceptionMessage('Large discrepancy between request timestamp and server time');

        $sign->verify($py);
    }

    public function testVerifyExceptionExpired(): void
    {
        $py = Payload::forVerify()
            ->setAppId('123')
            ->setPath('/path')
            ->setMethod('post')
            ->setNonce('1111')
            ->setTimestamp(time())
            ->setSign('xxxxx')
            ->build();

        $cs = m::mock(\Illuminate\Contracts\Cache\Repository::class);
        $cs->shouldReceive('get')->once()->with('laravel_signature:xxxxx')->andReturn(1);
        $cf = m::mock(Factory::class);
        $cf->shouldReceive('store')->once()->andReturn($cs);

        $sign = new DefaultSignature($cf);
        $sign->setConfig([
            'cache_driver' => 'fake'
        ]);

        $this->expectException(VerifyException::class);
        $this->expectExceptionMessage('The signature has expired');

        $sign->verify($py);
    }

    public function testVerifyExceptionMismatch(): void
    {
        $py = Payload::forVerify()
            ->setAppId('123')
            ->setPath('/path')
            ->setMethod('post')
            ->setNonce('1111')
            ->setTimestamp(time())
            ->setSign('xxxxx')
            ->build();

        $def = new Define('123', '123', 'aaaa', []);

        $rp = m::mock(Repository::class);
        $rp->shouldReceive('findByAppId')->once()->with('123')->andReturn($def);

        $sign = new DefaultSignature();
        $sign->setRepository($rp);

        $this->expectException(VerifyException::class);
        $this->expectExceptionMessage('Signature mismatch');

        $sign->verify($py);
    }
}