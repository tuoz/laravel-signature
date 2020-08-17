<?php


namespace Hypocenter\LaravelSignature\Define\Models;


use Hypocenter\LaravelSignature\Define\Define;
use Hypocenter\LaravelSignature\Define\IntoDefine;
use Illuminate\Database\Eloquent\Model;

/**
 * 默认的基于 Laravel ORM 的模型，用于使用数据库存储 App 定义
 * 也可以自定义模型，只需实现 IntoDefine 接口即可
 *
 * @property string $name
 * @property string $id
 * @property string $secret
 * @property array $config
 *
 * @package Hypocenter\LaravelSignature\Models
 * @see ../../../database/migrations/0000_00_00_000000_signature_create_app_defines_table.php
 */
class AppDefine extends Model implements IntoDefine
{
    protected $casts = [
        'config' => 'json'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function intoSignatureDefine(): Define
    {
        return new Define(
            $this->id,
            $this->name,
            $this->secret,
            $this->config
        );
    }
}