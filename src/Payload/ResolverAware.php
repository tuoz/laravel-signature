<?php


namespace Hypocenter\LaravelSignature\Payload;


interface ResolverAware
{
    public function setResolver(Resolver $resolver);
}