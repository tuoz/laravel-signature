<?php


namespace Hypocenter\LaravelSignature\Define;


interface RepositoryAware
{
    public function setRepository(Repository  $repository);
}