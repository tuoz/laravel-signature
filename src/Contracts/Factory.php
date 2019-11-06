<?php


namespace Hypocenter\LaravelSignature\Contracts;


use Hypocenter\LaravelSignature\Interfaces\Driver;

interface Factory
{
    public function driver($name = null): Driver;
}