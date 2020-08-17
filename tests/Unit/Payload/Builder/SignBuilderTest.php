<?php


namespace Hypocenter\LaravelSignature\Tests\Unit\Payload\Builder;


use Hypocenter\LaravelSignature\Exceptions\InvalidArgumentException;
use Hypocenter\LaravelSignature\Payload\Payload;
use PHPUnit\Framework\TestCase;

class SignBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $builder = Payload::forSign();

        $appID = 'appid';
        $path = '/path';
        $method = 'post';
        $data = ['data' => 1];
        $ts = time();

        $builder->setAppId($appID);
        $builder->setPath($path);
        $builder->setData($data);
        $builder->setMethod($method);
        $builder->setTimestamp($ts);

        $py = $builder->build();

        $this->assertEquals($appID, $py->getAppId());
        $this->assertEquals($path, $py->getPath());
        $this->assertEquals($method, $py->getMethod());
        $this->assertEquals($data, $py->getData());
        $this->assertEquals($ts, $py->getTimestamp());
        $this->assertEmpty($py->getNonce());
        $this->assertEmpty($py->getSign());
    }

    public function testPathException(): void
    {
        $builder = Payload::forSign();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "path" must not be empty');

        $builder->build();
    }

    public function testMethodException(): void
    {
        $builder = Payload::forSign();
        $builder->setPath('/path');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('the "method" must not be empty');

        $builder->build();
    }
}