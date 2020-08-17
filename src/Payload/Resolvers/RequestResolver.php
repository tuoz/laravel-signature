<?php


namespace Hypocenter\LaravelSignature\Payload\Resolvers;


use Hypocenter\LaravelSignature\Payload\Payload;
use Hypocenter\LaravelSignature\Payload\Resolver;

abstract class RequestResolver implements Resolver
{
    /**
     * @var RequestProxy
     */
    protected $request;

    public function __construct(RequestProxy $request)
    {
        $this->request = $request;
    }

    public function resolvePayload(): Payload
    {
        return Payload::forVerify()
            ->setAppId($this->getAppId())
            ->setSign($this->getSign())
            ->setTimestamp($this->getTimestamp())
            ->setNonce($this->getNonce())
            ->setPath($this->getPath())
            ->setMethod($this->getMethod())
            ->setData($this->getData())
            ->build();
    }

    abstract protected function getAppId(): ?string;

    abstract protected function getSign(): ?string;

    abstract protected function getNonce(): ?string;

    abstract protected function getTimestamp(): ?string;

    protected function getMethod(): ?string
    {
        return $this->request->method();
    }

    protected function getPath(): ?string
    {
        return $this->request->path();
    }

    protected function getData(): ?array
    {
        return $this->request->all();
    }
}