<?php


namespace Hypocenter\LaravelSignature\Resolvers;


class HeaderResolver extends RequestResolver
{
    /**
     * @var array
     */
    private $config = [
        'key_app_id'    => 'X-SIGN-APP-ID',
        'key_sign'      => 'X-SIGN',
        'key_timestamp' => 'X-SIGN-TIME',
        'key_nonce'     => 'X-SIGN-NONCE',
    ];

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

    private function httpHeader($k)
    {
        return $this->request->header($k);
    }
}