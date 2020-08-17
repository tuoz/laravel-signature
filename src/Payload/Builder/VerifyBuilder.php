<?php


namespace Hypocenter\LaravelSignature\Payload\Builder;


use Hypocenter\LaravelSignature\Exceptions\InvalidArgumentException;
use Hypocenter\LaravelSignature\Payload\Payload;

class VerifyBuilder extends SignBuilder
{
    public function setSign(?string $sign): self
    {
        $this->payload->setSign($sign);
        return $this;
    }

    public function setNonce(?string $nonce): self
    {
        $this->payload->setNonce($nonce);
        return $this;
    }

    public function build(): Payload
    {
        if (empty($this->payload->getAppId())) {
            throw new InvalidArgumentException('the "appId" must not be empty');
        }
        if (empty($this->payload->getPath())) {
            throw new InvalidArgumentException('the "path" must not be empty');
        }
        if (empty($this->payload->getMethod())) {
            throw new InvalidArgumentException('the "method" must not be empty');
        }
        if (empty($this->payload->getSign())) {
            throw new InvalidArgumentException('the "sign" must not be empty');
        }
        if (empty($this->payload->getTimestamp())) {
            throw new InvalidArgumentException('the "timestamp" must not be empty');
        }
        if (empty($this->payload->getNonce())) {
            throw new InvalidArgumentException('the "nonce" must not be empty');
        }

        return $this->payload;
    }
}