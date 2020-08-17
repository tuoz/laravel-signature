<?php


namespace Hypocenter\LaravelSignature\Tests\Unit\Payload\Builder;


use Hypocenter\LaravelSignature\Exceptions\InvalidArgumentException;
use Hypocenter\LaravelSignature\Payload\Payload;
use PHPUnit\Framework\TestCase;

class VerifyBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $builder = Payload::forVerify();

        $appID = 'appid';
        $path = '/path';
        $method = 'post';
        $data = ['data' => 1];
        $ts = time();
        $sign = 'sign';
        $nonce = 'nonce';

        $builder->setAppId($appID);
        $builder->setPath($path);
        $builder->setData($data);
        $builder->setMethod($method);
        $builder->setTimestamp($ts);
        $builder->setSign($sign);
        $builder->setNonce($nonce);

        $py = $builder->build();

        $this->assertEquals($appID, $py->getAppId());
        $this->assertEquals($path, $py->getPath());
        $this->assertEquals($method, $py->getMethod());
        $this->assertEquals($data, $py->getData());
        $this->assertEquals($ts, $py->getTimestamp());
        $this->assertEquals($sign, $py->getSign());
        $this->assertEquals($nonce, $py->getNonce());
    }

    public function testException(): void
    {
        $builder = Payload::forVerify();

        $this->exceptInvalidArgumentException('the "appId" must not be empty', static function () use ($builder) {
               $builder->build();
        });
        $this->exceptInvalidArgumentException('the "path" must not be empty', static function () use ($builder) {
            $builder->setAppId('appid');
            $builder->build();
        });
        $this->exceptInvalidArgumentException('the "method" must not be empty', static function () use ($builder) {
            $builder->setPath('/path');
            $builder->build();
        });
        $this->exceptInvalidArgumentException('the "sign" must not be empty', static function () use ($builder) {
            $builder->setMethod('post');
            $builder->build();
        });
        $this->exceptInvalidArgumentException('the "timestamp" must not be empty', static function () use ($builder) {
            $builder->setSign('sign');
            $builder->build();
        });
        $this->exceptInvalidArgumentException('the "nonce" must not be empty', static function () use ($builder) {
            $builder->setTimestamp(time());
            $builder->build();
        });
    }

    private function exceptInvalidArgumentException($msg, $cbk): void
    {
        $isThrow = false;
        $cls = InvalidArgumentException::class;
        try {
            $cbk();
        } catch (\Throwable $e) {
            $isThrow = true;
            $this->assertInstanceOf($cls, $e);
            if ($msg) {
                $this->assertEquals($msg, $e->getMessage());
            }
        }

        $this->assertTrue($isThrow);
    }
}