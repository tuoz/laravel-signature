<?php


namespace Hypocenter\LaravelSignature\Interfaces;


use Hypocenter\LaravelSignature\Entities\Payload;

interface Driver extends SetConfig
{
    public function setResolver(?Resolver $resolver);

    public function setRepository(?Repository $repository);

    public function sign(Payload $payload): string;

    public function verify(Payload $payload): bool;
}