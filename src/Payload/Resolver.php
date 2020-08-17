<?php


namespace Hypocenter\LaravelSignature\Payload;


interface Resolver
{
    public function resolvePayload(): Payload;
}