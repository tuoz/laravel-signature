<?php


namespace Hypocenter\LaravelSignature\Define;

interface Repository
{
    public function findByAppId($appId): ?Define;
}