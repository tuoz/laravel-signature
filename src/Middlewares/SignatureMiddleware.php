<?php


namespace Hypocenter\LaravelSignature\Middlewares;


use Closure;
use Hypocenter\LaravelSignature\Contracts\Factory;
use Hypocenter\LaravelSignature\Exceptions\InvalidArgumentException;
use Hypocenter\LaravelSignature\Exceptions\VerifyException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SignatureMiddleware
{
    /**
     * @var Factory
     */
    private $signatureManager;

    public function __construct(Factory $signatureManager)
    {
        $this->signatureManager = $signatureManager;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @param null $signatureName
     * @return mixed
     * @throws \Exception|\Psr\SimpleCache\InvalidArgumentException
     */
    public function handle(Request $request, Closure $next, $signatureName = null)
    {
        try {
            $signature = $this->signatureManager->get($signatureName);
            $payload = $signature->resolve();
            $signature->verify($payload);
        } catch (VerifyException $e) {
            throw new HttpException(400, $e->getMessage(), $e);
        } catch (InvalidArgumentException $e) {
            throw new HttpException(400, $e->getMessage(), $e);
        }

        return $next($request);
    }
}