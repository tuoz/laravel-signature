<?php


namespace Hypocenter\LaravelSignature\Define\Repositories;


use Hypocenter\LaravelSignature\Define\Define;
use Hypocenter\LaravelSignature\Define\Repository;
use Hypocenter\LaravelSignature\Interfaces\Configurator;

class ArrayRepository implements Configurator, Repository
{
    private $defines = [];

    public function setConfig(array $config): void
    {
        if (isset($config['defines'])) {
            $this->defines = $config['defines'];
        }
    }

    public function findByAppId($appId): ?Define
    {
        $def = collect($this->defines)->firstWhere('id', $appId);
        if (!$def) {
            return null;
        }

        return new Define(
            $def['id'],
            $def['name'],
            $def['secret'],
            $def['config']
        );
    }
}