<?php


namespace Hypocenter\LaravelSignature\Resolvers;


use Hypocenter\LaravelSignature\Interfaces\Resolver;
use Illuminate\Http\Request;

abstract class RequestResolver implements Resolver
{
    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getMethod(): ?string
    {
        return $this->request->method();
    }

    public function getPath(): ?string
    {
        return $this->request->path();
    }

    public function getData(): ?array
    {
        return $this->request->all();
    }
}