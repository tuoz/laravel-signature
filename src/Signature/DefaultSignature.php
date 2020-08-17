<?php


namespace Hypocenter\LaravelSignature\Signature;


use Exception;
use Hypocenter\LaravelSignature\Define\RepositoryAware;
use Hypocenter\LaravelSignature\Payload\ResolverAware;
use Hypocenter\LaravelSignature\Signature\Context;
use Hypocenter\LaravelSignature\Define\Define;
use Hypocenter\LaravelSignature\Define\Repository;
use Hypocenter\LaravelSignature\Exceptions\InvalidArgumentException;
use Hypocenter\LaravelSignature\Exceptions\VerifyException;
use Hypocenter\LaravelSignature\Interfaces\Configurator;
use Hypocenter\LaravelSignature\Payload\Payload;
use Hypocenter\LaravelSignature\Payload\Resolver;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Support\Facades\Cache;

class DefaultSignature implements Signature, Configurator, RepositoryAware, ResolverAware
{
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var Resolver
     */
    private $resolver;

    private $nonceLength = 16;

    private $cacheDriver;

    private $cacheName = 'laravel_signature';

    private $timeTolerance = 5 * 60;

    private $defaultAppId;
    /**
     * @var Factory|null
     */
    private $cacheFactory;

    public function __construct(?Factory $cache = null)
    {
        $this->cacheFactory = $cache;
    }

    public function setConfig(array $config): void
    {
        if (isset($config['nonce_length'])) {
            $this->nonceLength = (int)$config['nonce_length'];
        }
        if (isset($config['cache_driver'])) {
            $this->cacheDriver = $config['cache_driver'];
        }
        if (isset($config['time_tolerance'])) {
            $this->timeTolerance = $config['time_tolerance'];
        }
        if (isset($config['cache_name'])) {
            $this->cacheName = $config['cache_name'];
        }
        if (isset($config['default_app_id'])) {
            $this->defaultAppId = $config['default_app_id'];
        }
    }

    public function setResolver(Resolver $resolver): void
    {
        $this->resolver = $resolver;
    }

    public function setRepository(Repository $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * 从请求中获取待校验数据
     *
     * @return Payload
     */
    public function resolve(): Payload
    {
        return $this->resolver->resolvePayload();
    }

    /**
     * 签名
     *
     * @param Payload $payload
     * @return Context
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function sign(Payload $payload): Context
    {
        if ($payload->getTimestamp() === null) {
            $payload->setTimestamp(time());
        }
        if ($payload->getNonce() === null) {
            $payload->setNonce($this->nonce($this->nonceLength));
        }
        if ($payload->getAppId() === null) {
            $payload->setAppId($this->defaultAppId);
        }

        $define = $this->getAppDefine($payload->getAppId());

        $ctx = new Context($payload, $define);

        $this->doSign($ctx);
        $payload->setSign($ctx->getSign());

        return $ctx;
    }

    /**
     * 校验
     *
     * @param Payload $payload
     * @return Context
     * @throws Exception|\Psr\SimpleCache\InvalidArgumentException
     */
    public function verify(Payload $payload): Context
    {
        $ctx = new Context($payload);

        if (abs(time() - $payload->getTimestamp()) > $this->timeTolerance) {
            throw new VerifyException('Large discrepancy between request timestamp and server time', $ctx);
        }

        if ($this->cacheDriver && $this->cache()->get($this->cacheKey($payload->getSign()))) {
            throw new VerifyException('The signature has expired', $ctx);
        }

        $ctx->setDefine($this->getAppDefine($payload->getAppId()));
        $this->doSign($ctx);

        if ($ctx->getSign() !== $payload->getSign()) {
            throw new VerifyException('Signature mismatch', $ctx);
        }

        // 防止重放
        if ($this->cacheDriver) {
            $this->cache()->set($this->cacheKey($payload->getSign()), 1, $this->timeTolerance * 2 + 1);
        }

        return $ctx;
    }

    private function getAppDefine(string $appId): Define
    {
        $define = $this->repository->findByAppId($appId);
        if (!$define) {
            throw new InvalidArgumentException('app define "' . $appId . '" not found');
        }

        return $define;
    }

    private function doSign(Context $ctx): void
    {
        $payload = $ctx->getPayload();
        $define = $ctx->getDefine();

        $data = (array)$payload->getData();

        assert(!$define, 'signature app define can not be empty');

        $signArr = [
            $payload->getAppId(),
            $define->getSecret(),
            $payload->getTimestamp(),
            strtolower($payload->getMethod()),
            $payload === '/' ? '/' : strtolower(trim($payload->getPath(), '/')),
            $this->arr2str($data),
            $payload->getNonce(),
        ];

        $raw = implode('|', $signArr);
        $ctx->setRaw($raw);

        $sign = hash_hmac('sha1', $raw, $define->getSecret());

        $ctx->setSign($sign);
    }

    private function arr2str(?array &$data)
    {
        if (!$data) {
            return '';
        }

        $str = [];

        ksort($data);
        foreach ($data as $i => &$v) {
            $str[] = "{$i}:" . (is_array($v) ? '[' . $this->arr2str($v) . ']' : $v);
        }

        return join(';', $str);
    }

    /**
     * @param $len
     * @return string
     * @throws Exception
     */
    private function nonce($len)
    {
        $seeds = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
        $nonce = '';
        for ($i = 0; $i < $len; $i++) {
            $nonce .= $seeds[random_int(0, 61)];
        }

        return $nonce;
    }

    private function cache(): \Illuminate\Contracts\Cache\Repository
    {
        return $this->cacheFactory->store($this->cacheDriver);
    }

    private function cacheKey($key): string
    {
        return "{$this->cacheName}:{$key}";
    }
}