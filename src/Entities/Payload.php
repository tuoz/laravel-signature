<?php


namespace Hypocenter\LaravelSignature\Entities;


class Payload
{
    /**
     * @var array 数据
     */
    private $data;
    /**
     * @var string
     */
    private $appId;
    /**
     * @var string 签名
     */
    private $sign;
    /**
     * @var int 时间戳
     */
    private $timestamp;
    /**
     * @var string 随机字符串
     */
    private $nonce;
    /**
     * @var string 请求路径
     */
    private $path;
    /**
     * @var string 请求方法
     */
    private $method;
    /**
     * @var ?string
     */
    private $raw;

    private $failedReason;

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return Payload
     */
    public function setData(array $data): Payload
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @param string $appId
     * @return Payload
     */
    public function setAppId(string $appId): Payload
    {
        $this->appId = $appId;
        return $this;
    }

    /**
     * @return string
     */
    public function getSign(): string
    {
        return $this->sign;
    }

    /**
     * @param string $sign
     * @return Payload
     */
    public function setSign(string $sign): Payload
    {
        $this->sign = $sign;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    /**
     * @param int $timestamp
     * @return Payload
     */
    public function setTimestamp(int $timestamp): Payload
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return string
     */
    public function getNonce(): string
    {
        return $this->nonce;
    }

    /**
     * @param string $nonce
     * @return Payload
     */
    public function setNonce(string $nonce): Payload
    {
        $this->nonce = $nonce;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return Payload
     */
    public function setPath(string $path): Payload
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return Payload
     */
    public function setMethod(string $method): Payload
    {
        $this->method = $method;
        return $this;
    }

    /**
     * 是否已签名
     * @return bool
     */
    public function isSigned()
    {
        return !!$this->sign;
    }

    /**
     * @return mixed
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * @param mixed $raw
     * @return Payload
     */
    public function setRaw($raw)
    {
        $this->raw = $raw;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFailedReason()
    {
        return $this->failedReason;
    }

    /**
     * @param mixed $failedReason
     * @return Payload
     */
    public function setFailedReason($failedReason)
    {
        $this->failedReason = $failedReason;
        return $this;
    }
}