<?php


namespace Hypocenter\LaravelSignature\Interfaces;


use Hypocenter\LaravelSignature\Define;

interface Repository extends SetConfig
{
    public function findByAppId($appId): ?Define;
}