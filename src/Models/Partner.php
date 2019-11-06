<?php


namespace Hypocenter\LaravelSignature\Models;


use Hypocenter\LaravelSignature\Interfaces\ToDefine;
use Hypocenter\LaravelSignature\Define;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Partner
 *
 * @property string $name
 * @property string $key
 * @property string $secret
 * @property string $config
 *
 * @package Hypocenter\LaravelSignature\Models
 */
class Partner extends Model implements ToDefine
{
    protected $casts = [
        'config' => 'json'
    ];

    public function toDefine(): Define
    {
        return new Define($this->name, $this->key, $this->secret, $this->config);
    }
}