<?php


namespace Hypocenter\LaravelSignature;


use Hypocenter\LaravelSignature\Contracts\Factory;
use Hypocenter\LaravelSignature\Define\Repository;
use Hypocenter\LaravelSignature\Define\RepositoryAware;
use Hypocenter\LaravelSignature\Exceptions\InvalidArgumentException;
use Hypocenter\LaravelSignature\Interfaces\Configurator;
use Hypocenter\LaravelSignature\Payload\Resolver;
use Hypocenter\LaravelSignature\Payload\ResolverAware;
use Hypocenter\LaravelSignature\Signature\DefaultSignature;
use Hypocenter\LaravelSignature\Signature\Signature;
use Illuminate\Container\Container;
use Illuminate\Support\Arr;

class SignatureManager implements Factory
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var Signature[]
     */
    private $instances = [];
    /**
     * @var Container
     */
    private $app;

    public function __construct(array $config, Container $app)
    {
        $this->config = $config;
        $this->app = $app;
    }

    public function get($name = null): Signature
    {
        $name = $name ?: data_get($this->config, 'default', 'default');

        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        $signature = $this->resolveSignature($name);
        $this->instances[$name] = $signature;

        return $signature;
    }

    private function resolveSignature($name): Signature
    {
        $c = data_get($this->config, "signatures.{$name}");
        if (!$c) {
            throw new InvalidArgumentException("no $name signature");
        }

        /** @var Signature $signature */
        $signature = $this->app->make(data_get($c, 'class') ?: DefaultSignature::class);

        $this->applyConfigurator($signature, Arr::except($c, ['class', 'resolver', 'repository']));

        if ($signature instanceof ResolverAware && data_get($c, 'resolver')) {
            $signature->setResolver($this->resolvePayloadResolver(data_get($c, 'resolver')));
        }

        if ($signature instanceof RepositoryAware && data_get($c, 'repository')) {
            $signature->setRepository($this->resolveDefineRepository(data_get($c, 'repository')));
        }

        return $signature;
    }

    private function resolvePayloadResolver($name): Resolver
    {
        $c = data_get($this->config, "resolvers.{$name}");
        if (!$c) {
            throw new InvalidArgumentException("no $name resolver");
        }

        /** @var Resolver $resolver */
        $resolver = $this->app->make(data_get($c, 'class'));
        $this->applyConfigurator($resolver, Arr::except($c, 'class'));

        return $resolver;
    }

    private function resolveDefineRepository($name): Repository
    {
        $c = data_get($this->config, "repositories.{$name}");
        if (!$c) {
            throw new InvalidArgumentException("no $name repository");
        }

        /** @var Repository $repository */
        $repository = $this->app->make(data_get($c, 'class'));
        $this->applyConfigurator($repository, Arr::except($c, 'class'));

        return $repository;
    }

    private function applyConfigurator($cls, array $config): void
    {
        if ($cls instanceof Configurator) {
            $cls->setConfig($config);
        }
    }
}