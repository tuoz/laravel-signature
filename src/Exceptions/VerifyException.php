<?php


namespace Hypocenter\LaravelSignature\Exceptions;


use Hypocenter\LaravelSignature\Entities\Payload;
use Symfony\Component\HttpKernel\Exception\HttpException;

class VerifyException extends HttpException
{
    /**
     * @var null|Payload
     */
    private $payload;

    /**
     *
     * @param string|null $message
     * @param Payload|null $payload
     * @param \Exception|null $previous
     * @param array $headers
     * @param int $code
     */
    public function __construct($message = null, Payload $payload = null, \Exception $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct(400, $message, $previous, $headers, $code);
        $this->payload = $payload;
    }

    public function getPayload(): ?Payload
    {
        return $this->payload;
    }
}