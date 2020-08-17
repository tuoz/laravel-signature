<?php


namespace Hypocenter\LaravelSignature\Contracts;


use Hypocenter\LaravelSignature\Signature\Signature;

interface Factory
{
    public function get($name = null): Signature;
}