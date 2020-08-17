<?php


namespace Hypocenter\LaravelSignature\Payload\Builder;


use Hypocenter\LaravelSignature\Exceptions\InvalidArgumentException;
use Hypocenter\LaravelSignature\Payload\Payload;

class SignBuilder
{
    /**
     * @var Payload
     */
    protected $payload;

    public function __construct(Payload $payload)
    {
        $this->payload = $payload;
    }

    public function setAppId(?string $appId): self
    {
        $this->payload->setAppId($appId);
        return $this;
    }

    public function setPath(?string $path): self
    {
        $this->payload->setPath($path);
        return $this;
    }

    public function setMethod(?string $method): self
    {
        $this->payload->setMethod($method);
        return $this;
    }

    public function setData(?array $data): self
    {
        $this->payload->setData($data);
        return $this;
    }

    public function setTimestamp(?string $ts): self
    {
        $this->payload->setTimestamp($ts);
        return $this;
    }

    public function build(): Payload
    {
        if (empty($this->payload->getPath())) {
            throw new InvalidArgumentException('the "path" must not be empty');
        }
        if (empty($this->payload->getMethod())) {
            throw new InvalidArgumentException('the "method" must not be empty');
        }

        return $this->payload;
    }
}