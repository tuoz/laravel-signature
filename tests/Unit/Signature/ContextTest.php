<?php


namespace Hypocenter\LaravelSignature\Tests\Unit\Signature;


use Hypocenter\LaravelSignature\Define\Define;
use Hypocenter\LaravelSignature\Payload\Payload;
use Hypocenter\LaravelSignature\Signature\Context;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class ContextTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testGetterSetter(): void
    {
        $payload = m::spy(Payload::class);
        $ctx = new Context($payload);
        $this->assertEquals($payload, $ctx->getPayload());

        $def = m::spy(Define::class);
        $ctx = new Context($payload, $def);
        $this->assertEquals($def, $ctx->getDefine());

        $this->assertEquals($ctx, $ctx->setSign('123'));
        $this->assertEquals('123', $ctx->getSign());

        $this->assertEquals($ctx, $ctx->setRaw('123'));
        $this->assertEquals('123', $ctx->getRaw());

        $this->assertEquals($ctx, $ctx->setDefine($def));
        $this->assertEquals($def, $ctx->getDefine());
    }
}