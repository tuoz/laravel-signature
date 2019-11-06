<?php


namespace Hypocenter\LaravelSignature\Middleware;


use Closure;
use Hypocenter\LaravelSignature\Contracts\Factory;
use Hypocenter\LaravelSignature\Entities\Payload;
use Hypocenter\LaravelSignature\Exceptions\VerifyException;

class SignatureMiddleware
{
    /**
     * @var Factory
     */
    private $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function handle($request, Closure $next, $driver = null)
    {
        $driver = $this->factory->driver($driver);
        $payload = new Payload();
        $payload->setData($request->all());
        if (!$driver->verify($payload)) {
            throw new VerifyException("签名验证失败", $payload);
        }

        return $next($request);
    }
}