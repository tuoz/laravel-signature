<?php


namespace Hypocenter\LaravelSignature\Repositories;


use Hypocenter\LaravelSignature\Interfaces\Repository;
use Hypocenter\LaravelSignature\Define;

class ArrayRepository implements Repository
{
    private $defines = [];

    public function findByAppId($appId): ?Define
    {
        $def = collect($this->defines)->firstWhere('id', $appId);
        return new Define($def['id'], $def['name'], $def['secret'], $def['config']);
    }

    public function setConfig(array $config)
    {
        if (!empty($config['defines'])) {
            $this->defines = [];
        }
    }
}