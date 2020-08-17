<?php


namespace Hypocenter\LaravelSignature\Tests\Unit\Define;


use Hypocenter\LaravelSignature\Define\Define;
use PHPUnit\Framework\TestCase;

class DefineTest extends TestCase
{
    public function testConstruct(): void
    {
        $def = new Define('id', 'name', 'secret', ['scopes' => ['list', 'add']]);
        $this->assertEquals('id', $def->getId());
        $this->assertEquals('name', $def->getName());
        $this->assertEquals('secret', $def->getSecret());
        $this->assertEquals(['scopes' => ['list', 'add']], $def->getConfig());
    }
}