<?php


namespace Hypocenter\LaravelSignature\Signature;


use Hypocenter\LaravelSignature\Define\Define;
use Hypocenter\LaravelSignature\Payload\Payload;

class Context
{
    /**
     * @var Payload
     */
    private $payload;
    /**
     * @var null|string
     */
    private $sign;
    /**
     * @var null|string
     */
    private $raw;
    /**
     * @var null|Define
     */
    private $define;

    public function __construct(Payload $payload, ?Define $define = null)
    {
        $this->payload = $payload;
        $this->define = $define;
    }

    /**
     * @return Payload
     */
    public function getPayload(): Payload
    {
        return $this->payload;
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
     * @return Context
     */
    public function setSign(?string $sign): Context
    {
        $this->sign = $sign;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRaw(): ?string
    {
        return $this->raw;
    }

    /**
     * @param string|null $raw
     * @return Context
     */
    public function setRaw(?string $raw): Context
    {
        $this->raw = $raw;
        return $this;
    }

    /**
     * @return Define|null
     */
    public function getDefine(): ?Define
    {
        return $this->define;
    }

    /**
     * @param Define $define
     * @return Context
     */
    public function setDefine(Define $define): Context
    {
        $this->define = $define;
        return $this;
    }
}