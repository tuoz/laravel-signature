<?php


namespace Hypocenter\LaravelSignature\Tests\Unit\Payload\Resolvers;


use Hypocenter\LaravelSignature\Payload\Resolvers\RequestProxy;

class RequestProxyTest extends \Orchestra\Testbench\TestCase
{
    public function testAll(): void
    {
        $p = new RequestProxy();

        $p->method();
        $p->all();
        $this->assertEquals('localhost', $p->header('host'));
    }
}