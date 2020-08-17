<?php


namespace Hypocenter\LaravelSignature\Tests\Unit;


use Hypocenter\LaravelSignature\Define\Repository;
use Hypocenter\LaravelSignature\Define\RepositoryAware;
use Hypocenter\LaravelSignature\Exceptions\InvalidArgumentException;
use Hypocenter\LaravelSignature\Interfaces\Configurator;
use Hypocenter\LaravelSignature\Payload\Resolver;
use Hypocenter\LaravelSignature\Payload\ResolverAware;
use Hypocenter\LaravelSignature\Signature\DefaultSignature;
use Hypocenter\LaravelSignature\Signature\Signature;
use Hypocenter\LaravelSignature\SignatureManager;
use Illuminate\Container\Container;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class SignatureManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testGet(): void
    {
        $app = m::mock(Container::class);
        $app->shouldReceive('make')->once()->with('signature')->andReturn(m::spy(Signature::class));

        $m = new SignatureManager([
            'default' => 'default',
            'signatures'   => [
                'default' => [
                    'class'      => 'signature',
                ],
            ],
        ], $app);

        $this->assertEquals($m->get('default'), $m->get());

        $this->expectException(InvalidArgumentException::class);
        $m->get('undefined');
    }

    public function testResolveSignatureResolveUndefinedClass(): void
    {
        $rs = m::mock(Resolver::class);
        $rp = m::mock(Repository::class);

        $sg = m::mock(Signature::class, Configurator::class, ResolverAware::class, RepositoryAware::class);
        $sg->shouldReceive('setConfig')->once();
        $sg->shouldReceive('setResolver', function ($v) use ($rs) {
            $this->assertEquals($rs, $v);
        })->once();
        $sg->shouldReceive('setRepository', function ($v) use ($rp) {
            $this->assertEquals($rp, $v);
        })->once();

        $app = m::mock(Container::class);
        $app->shouldReceive('make')->once()->with('resolver')->andReturn($rs);
        $app->shouldReceive('make')->once()->with('repository')->andReturn($rp);
        $app->shouldReceive('make')->once()->with('signature')->andReturn($sg);

        $manager = new SignatureManager([
            'signatures'   => [
                'default' => [
                    'class'      => 'signature',
                    'resolver'   => 'test',
                    'repository' => 'test'
                ],
            ],
            'resolvers'    => [
                'test' => [
                    'class' => 'resolver'
                ]
            ],
            'repositories' => [
                'test' => [
                    'class' => 'repository'
                ]
            ]
        ], $app);

        $signature = (function () {
            return $this->resolveSignature('default');
        })->call($manager);

        $this->assertEquals($signature, $sg);

        $this->expectException(InvalidArgumentException::class);
        (function () {
            return $this->resolveSignature('undefined');
        })->call($manager);
    }

    public function testResolveSignatureUseDefault(): void
    {
        $rs = m::mock(Resolver::class);
        $rp = m::mock(Repository::class);

        $app = m::mock(Container::class);
        $app->shouldReceive('make')->once()->with('resolver')->andReturn($rs);
        $app->shouldReceive('make')->once()->with('repository')->andReturn($rp);
        $app->shouldReceive('make')->once()->with(DefaultSignature::class)->andReturn(m::spy(DefaultSignature::class));

        $manager = new SignatureManager([
            'signatures'   => [
                'default' => [
                    'resolver'   => 'test',
                    'repository' => 'test'
                ],
            ],
            'resolvers'    => [
                'test' => [
                    'class' => 'resolver'
                ]
            ],
            'repositories' => [
                'test' => [
                    'class' => 'repository'
                ]
            ]
        ], $app);

        (function () {
            return $this->resolveSignature('default');
        })->call($manager);
    }

    public function testResolveDefineRepository(): void
    {
        $rp = m::mock(Repository::class, Configurator::class);
        $rp->shouldReceive('setConfig')->once();

        $app = m::mock(Container::class);
        $app->shouldReceive('make')->with('test')->andReturn($rp);

        $manager = new SignatureManager([
            'repositories' => [
                'test' => [
                    'class' => 'test',
                    'field' => 'value',
                ]
            ]
        ], $app);

        $resolver = (function () {
            return $this->resolveDefineRepository('test');
        })->call($manager);

        $this->assertEquals($resolver, $rp);

        $this->expectException(InvalidArgumentException::class);
        (function () {
            $this->resolveDefineRepository('undefined');
        })->call($manager);
    }

    public function testResolveDefineRepositoryNoSetConfig(): void
    {
        $rp = m::mock(Repository::class);
        $rp->shouldNotReceive('setConfig');
        $app = m::mock(Container::class);
        $app->shouldReceive('make')->with('test')->andReturn($rp);

        $manager = new SignatureManager([
            'repositories' => [
                'test' => ['class' => 'test']
            ]
        ], $app);

        (function () {
            $this->resolveDefineRepository('test');
        })->call($manager);
    }

    public function testResolvePayloadResolver(): void
    {
        $rs = m::mock(Resolver::class, Configurator::class);
        $rs->shouldReceive('setConfig')->once();

        $app = m::mock(Container::class);
        $app->shouldReceive('make')->with('test')->andReturn($rs);

        $manager = new SignatureManager([
            'resolvers' => [
                'test' => [
                    'class' => 'test',
                    'field' => 'value',
                ]
            ]
        ], $app);

        $resolver = (function () {
            return $this->resolvePayloadResolver('test');
        })->call($manager);

        $this->assertEquals($resolver, $rs);

        $this->expectException(InvalidArgumentException::class);
        (function () {
            $this->resolvePayloadResolver('undefined');
        })->call($manager);
    }

    public function testResolvePayloadResolverNoSetConfig(): void
    {
        $rs = m::mock(Resolver::class);
        $rs->shouldNotReceive('setConfig');
        $app = m::mock(Container::class);
        $app->shouldReceive('make')->with('test')->andReturn($rs);

        $manager = new SignatureManager([
            'resolvers' => [
                'test' => ['class' => 'test']
            ]
        ], $app);

        (function () {
            $this->resolvePayloadResolver('test');
        })->call($manager);
    }
}
