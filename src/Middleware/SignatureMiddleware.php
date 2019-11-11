<?php


namespace Hypocenter\LaravelSignature\Middleware;


use Closure;
use Hypocenter\LaravelSignature\Entities\Payload;
use Hypocenter\LaravelSignature\Exceptions\VerifyException;
use Hypocenter\LaravelSignature\Interfaces\Driver;
use Illuminate\Http\Request;

class SignatureMiddleware
{
    public function handle(Request $request, Closure $next, $driver = null)
    {
        /** @var Driver $driver */
        $driver = app('signature')->driver($driver);
        $payload = new Payload();
        if (!$driver->verify($payload)) {
            throw new VerifyException("签名验证失败:" . $payload->getFailedReason(), $payload);
        }

        return $next($request);
    }
}