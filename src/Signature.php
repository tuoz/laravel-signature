<?php


namespace Hypocenter\LaravelSignature;


use Hypocenter\LaravelSignature\Interfaces\Repository;
use Hypocenter\LaravelSignature\Interfaces\Resolver;
use Hypocenter\LaravelSignature\Interfaces\Driver;
use Hypocenter\LaravelSignature\Entities\Payload;
use Illuminate\Support\Facades\Cache;

class Signature implements Driver
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

    private $time_tolerance = 5 * 60;

    public function __construct() { }

    public function setConfig(array $config)
    {
        if (isset($config['nonce_length'])) {
            $this->nonceLength = intval($config['nonce_length']);
        }
        if (isset($config['cache_driver'])) {
            $this->cacheDriver = $config['cache_driver'];
        }
        if (isset($config['time_tolerance'])) {
            $this->time_tolerance = $config['time_tolerance'];
        }
        if (isset($config['cache_name'])) {
            $this->cacheName = $config['cache_name'];
        }
    }

    public function setResolver(?Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function setRepository(?Repository $repository)
    {
        $this->repository = $repository;
    }

    public function getResolver(): ?Resolver
    {
        return $this->resolver;
    }

    public function getRepository(): ?Repository
    {
        return $this->repository;
    }

    public function sign(Payload $payload): string
    {
        if (!$payload->getAppId()) {
            throw new \InvalidArgumentException('no app ID');
        }

        $define = $this->repository->findByAppId($payload->getAppId());
        if (!$define) {
            throw new \InvalidArgumentException('app setting not found');
        }

        !$payload->getTimestamp() && $payload->setTimestamp(time());
        !$payload->getNonce() && $payload->setNonce($this->nonce($this->nonceLength));

        $data = (array)$payload->getData();

        $signArr = [
            $payload->getAppId(),
            $define->getSecret(),
            $payload->getTimestamp(),
            strtolower($payload->getMethod()),
            strtolower($payload->getPath()),
            $this->arr2str($data),
            $payload->getNonce(),
        ];

        $raw = join('|', $signArr);
        $payload->setRaw($raw);

        $sign = hash_hmac('sha1', $raw, $define->getSecret());
        $payload->setSign($sign);

        return $sign;
    }

    public function verify(Payload $payload): bool
    {
        !$payload->getAppId() && $payload->setAppId($this->resolver->getAppId());
        !$payload->getTimestamp() && $payload->setTimestamp($this->resolver->getTimestamp());
        !$payload->getMethod() && $payload->setMethod($this->resolver->getMethod());
        !$payload->getPath() && $payload->setPath($this->resolver->getPath());
        !$payload->getNonce() && $payload->setNonce($this->resolver->getNonce());
        !$payload->getData() && $payload->setData($this->resolver->getData());

        if (!$payload->getAppId()) {
            $payload->setFailedReason('AppID 不能为空');
            return false;
        }
        if (!$payload->getTimestamp()) {
            $payload->setFailedReason('时间戳不能为空');
            return false;
        }
        if (!$payload->getPath()) {
            $payload->setFailedReason('请求路径不能为空');
            return false;
        }
        if (!$payload->getMethod()) {
            $payload->setFailedReason('请求方法不能为空');
            return false;
        }
        if (!$payload->getNonce()) {
            $payload->setFailedReason('随机数不能为空');
            return false;
        }

        if (abs(time() - $payload->getTimestamp()) > $this->time_tolerance) {
            $payload->setFailedReason('请求时间戳和服务器时间差异过大');
            return false;
        }

        $this->sign($payload);

        if ($payload->getSign() !== $this->resolver->getSign()) {
            $payload->setFailedReason('签名不匹配');
            return false;
        }

        if ($this->cache()->get($this->cacheKey($payload->getSign()))) {
            $payload->setFailedReason('签名已经过期');
            return false;
        }

        // 防止重放
        $this->cache()->set($this->cacheKey($payload->getSign()), 1, $this->time_tolerance * 2 + 1);

        return true;
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

    private function nonce($len)
    {
        $seeds = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
        $nonce = '';
        for ($i = 0; $i < $len; $i++) {
            $nonce .= $seeds[mt_rand(0, 61)];
        }

        return $nonce;
    }

    private function cache()
    {
        return Cache::store($this->cacheDriver);
    }

    private function cacheKey($key)
    {
        return "{$this->cacheName}:{$key}";
    }
}