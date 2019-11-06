<?php


namespace Hypocenter\LaravelSignature\Resolvers;


use Hypocenter\LaravelSignature\Interfaces\Resolver;
use Hypocenter\LaravelSignature\Entities\Payload;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HeaderResolver implements Resolver
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getAppId(): ?string
    {
        return $this->httpHeader(config('signature.resolvers.key_app_id'));
    }

    public function getSign(): ?string
    {
        return $this->httpHeader(config('signature.resolvers.key_sign'));
    }

    public function getTimestamp(): ?string
    {
        return $this->httpHeader(config('signature.resolvers.key_timestamp'));
    }

    public function getNonce(): ?string
    {
        return $this->httpHeader(config('signature.resolvers.key_nonce'));
    }

    public function getMethod(): ?string
    {
        return $this->request->method();
    }

    public function getPath(): ?string
    {
        return $this->request->path();
    }

    private function httpHeader($k)
    {
        return $_SERVER[$this->serverHttpKey($k)] ?? null;
    }

    private function serverHttpKey($key)
    {
        if (Str::startsWith($key, 'HTTP_')) {
            return $key;
        }

        return 'HTTP_' . $key;
    }
}