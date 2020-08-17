<?php


namespace Hypocenter\LaravelSignature\Payload\Resolvers;


use Hypocenter\LaravelSignature\Interfaces\Configurator;

class HeaderResolver extends RequestResolver implements Configurator
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

    public function setConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);
    }

    protected function getAppId(): ?string
    {
        return $this->httpHeader($this->config['key_app_id']);
    }

    protected function getSign(): ?string
    {
        return $this->httpHeader($this->config['key_sign']);
    }

    protected function getTimestamp(): ?string
    {
        return $this->httpHeader($this->config['key_timestamp']);
    }

    protected function getNonce(): ?string
    {
        return $this->httpHeader($this->config['key_nonce']);
    }

    private function httpHeader($k): ?string
    {
        return $this->request->header($k);
    }
}