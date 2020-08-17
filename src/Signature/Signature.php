<?php


namespace Hypocenter\LaravelSignature\Signature;


use Exception;
use Hypocenter\LaravelSignature\Exceptions\VerifyException;
use Hypocenter\LaravelSignature\Payload\Payload;

interface Signature
{
    /**
     * 从请求中获取待校验数据
     *
     * @return Payload
     */
    public function resolve(): Payload;

    /**
     * 签名
     *
     * @param Payload $payload
     * @return Context
     * @throw InvalidArgumentException
     */
    public function sign(Payload $payload): Context;

    /**
     * 校验
     *
     * @param Payload $payload
     * @return Context
     * @throws Exception|VerifyException
     */
    public function verify(Payload $payload): Context;
}