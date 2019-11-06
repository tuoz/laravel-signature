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
    /**
     * @var array
     */
    private $config = [
        'key_app_id'    => 'X-SIGN-APP-ID',
        'key_sign'      => 'X-SIGN',
        'key_timestamp' => 'X-SIGN-TIME',
        'key_nonce'     => 'X-SIGN-NONCE',
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }

    public function getAppId(): ?string
    {
        return $this->httpHeader($this->config['key_app_id']);
    }

    public function getSign(): ?string
    {
        return $this->httpHeader($this->config['key_sign']);
    }

    public function getTimestamp(): ?string
    {
        return $this->httpHeader($this->config['key_timestamp']);
    }

    public function getNonce(): ?string
    {
        return $this->httpHeader($this->config['key_nonce']);
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
        return $this->request->header($k);
    }
}