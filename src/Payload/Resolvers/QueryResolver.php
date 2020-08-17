<?php


namespace Hypocenter\LaravelSignature\Payload\Resolvers;


use Hypocenter\LaravelSignature\Interfaces\Configurator;

class QueryResolver extends RequestResolver implements Configurator
{
    private $config = [
        'key_app_id'    => '_appid',
        'key_sign'      => '_sign',
        'key_timestamp' => '_time',
        'key_nonce'     => '_nonce',
    ];

    public function setConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);
    }

    protected function getAppId(): ?string
    {
        return $this->request->get($this->config['key_app_id']);
    }

    protected function getSign(): ?string
    {
        return $this->request->get($this->config['key_sign']);
    }

    protected function getNonce(): ?string
    {
        return $this->request->get($this->config['key_nonce']);
    }

    protected function getTimestamp(): ?string
    {
        return $this->request->get($this->config['key_timestamp']);
    }

    protected function getData(): ?array
    {
        $data = parent::getData();
        $keys = ['key_app_id', 'key_sign', 'key_timestamp', 'key_nonce'];

        // 去掉用于传递控制信息的字段
        foreach ($keys as $k) {
            unset($data[$this->config[$k]]);
        }

        return $data;
    }

}