<?php


namespace Hypocenter\LaravelSignature\Payload\Resolvers;


use Illuminate\Http\Request;

/**
 * 在测试代码中, 每一次都需要使用独立的 Request
 * RequestResolver 构造函数中注入 Request 只能拿到启动的时候实例化的 Request
 * 后续集成测试中，单次请求的 Request 拿不到，所以需要使用一个动态代理
 * 每次都使用最新的 Request
 *
 * @mixin Request
 */
class RequestProxy
{
    public function __call($method, $args)
    {
        return call_user_func_array([app('request'), $method], $args);
    }
}