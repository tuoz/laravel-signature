<?php


namespace Hypocenter\LaravelSignature\Tests\Unit\Define\Repositories;


use Hypocenter\LaravelSignature\Define\Define;
use Hypocenter\LaravelSignature\Define\IntoDefine;
use Hypocenter\LaravelSignature\Define\Models\AppDefine;
use Hypocenter\LaravelSignature\Define\Repositories\ModelRepository;
use Hypocenter\LaravelSignature\Exceptions\InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

class ModelRepositoryTest extends TestCase
{
    public function testFindById(): void
    {
        $rp = new ModelRepository();
        $rp->setConfig([
            'model' => _Model::class
        ]);

        $def = $rp->findByAppId(1);
        $this->assertEquals(1, $def->getId());

        $def = $rp->findByAppId(2);
        $this->assertNull($def);
    }

    public function testSetConfigEmptyModel(): void
    {
        $rp = new ModelRepository();

        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('The model must not be null');

        $rp->setConfig([]);
    }

    public function testSetConfigNotModel(): void
    {
        $rp = new ModelRepository();

        $this->expectException(InvalidArgumentException::class);
        $this->expectErrorMessage('The model must be subclass of Model');

        $rp->setConfig([
            'model' => _IntoDefineNotModel::class
        ]);
    }
}

class _Model extends AppDefine
{
    public static function query()
    {
        return new self;
    }

    public function find($id): ?_Model
    {
        if ($id === 1) {
            return new self;
        }
        return null;
    }

    public function intoSignatureDefine(): Define
    {
        return new Define(1, 'name', 'secret', []);
    }
}

class _ModelNoIntoDefine extends Model
{
}

class _IntoDefineNotModel implements IntoDefine
{
    public function intoSignatureDefine(): Define
    {
        return new Define(1, 'name', 'secret', []);
    }
}