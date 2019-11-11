<?php


namespace Hypocenter\LaravelSignature\Entities;


class Payload
{
    /**
     * @var array|null 数据
     */
    private $data;
    /**
     * @var string|null
     */
    private $appId;
    /**
     * @var string|null 签名
     */
    private $sign;
    /**
     * @var int|null 时间戳
     */
    private $timestamp;
    /**
     * @var string|null 随机字符串
     */
    private $nonce;
    /**
     * @var string|null 请求路径
     */
    private $path;
    /**
     * @var string|null 请求方法
     */
    private $method;
    /**
     * @var string|null
     */
    private $raw;

    private $failedReason;

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param array|null $data
     * @return Payload
     */
    public function setData(?array $data): Payload
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAppId(): ?string
    {
        return $this->appId;
    }

    /**
     * @param string|null $appId
     * @return Payload
     */
    public function setAppId(?string $appId): Payload
    {
        $this->appId = $appId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSign(): ?string
    {
        return $this->sign;
    }

    /**
     * @param string|null $sign
     * @return Payload
     */
    public function setSign(?string $sign): Payload
    {
        $this->sign = $sign;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    /**
     * @param int|null $timestamp
     * @return Payload
     */
    public function setTimestamp(?int $timestamp): Payload
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNonce(): ?string
    {
        return $this->nonce;
    }

    /**
     * @param string|null $nonce
     * @return Payload
     */
    public function setNonce(?string $nonce): Payload
    {
        $this->nonce = $nonce;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     * @return Payload
     */
    public function setPath(?string $path): Payload
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @param string|null $method
     * @return Payload
     */
    public function setMethod(?string $method): Payload
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
     * @return null|string
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