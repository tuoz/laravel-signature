<?php


namespace Hypocenter\LaravelSignature\Tests\Unit\Define\Repositories;


use Hypocenter\LaravelSignature\Define\Define;
use Hypocenter\LaravelSignature\Define\Repositories\ArrayRepository;
use Hypocenter\LaravelSignature\Define\Repository;
use Hypocenter\LaravelSignature\Interfaces\Configurator;
use PHPUnit\Framework\TestCase;

class ArrayRepositoryTest extends TestCase
{
    public function testSetConfig(): void
    {
        $defines = [
            [
                'id'     => 1,
                'name'   => 'name',
                'secret' => 'secret',
                'config' => null
            ]
        ];

        $rp = new ArrayRepository();
        $this->assertInstanceOf(Configurator::class, $rp);

        $rp->setConfig([
            'defines' => $defines,
            'other'   => [
                'id' => 1
            ]
        ]);

        $returned = (function () {
            return $this->defines;
        })->call($rp);

        $this->assertEquals($defines, $returned);
    }

    public function testFindByAppId(): void
    {
        $defines = [
            [
                'id'     => 1,
                'name'   => 'name',
                'secret' => 'secret',
                'config' => null
            ],
            [
                'id'     => 2,
                'name'   => 'name2',
                'secret' => 'secret',
                'config' => [1, 2, 3],
            ]
        ];

        $rp = new ArrayRepository();
        $rp->setConfig(['defines' => $defines]);

        $this->assertInstanceOf(Repository::class, $rp);
        $this->assertInstanceOf(Define::class, $rp->findByAppId(1));

        $df2 = $rp->findByAppId(2);

        $this->assertEquals(2, $df2->getId());
        $this->assertEquals('name2', $df2->getName());
        $this->assertEquals('secret', $df2->getSecret());
        $this->assertEquals([1, 2, 3], $df2->getConfig());

        $this->assertEmpty($rp->findByAppId(3));
    }
}