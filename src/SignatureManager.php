<?php


namespace Hypocenter\LaravelSignature;


use Hypocenter\LaravelSignature\Contracts\Factory;
use Hypocenter\LaravelSignature\Interfaces\Repository;
use Hypocenter\LaravelSignature\Interfaces\Resolver;
use Hypocenter\LaravelSignature\Interfaces\Driver;
use Illuminate\Support\Arr;

class SignatureManager implements Factory
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var Driver[]
     */
    private $drivers = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function driver($name = null): Driver
    {
        $name = $name ?: data_get($this->config, 'default', 'default');

        if (isset($this->driver[$name])) {
            return $this->drivers[$name];
        }

        $driver = $this->resolveDriver($name);
        $this->drivers[$name] = $driver;

        return $driver;
    }

    private function resolveDriver($name): Driver
    {
        $c = data_get($this->config, "drivers.{$name}");
        if (!$c) {
            throw new \InvalidArgumentException("no $name driver");
        }

        /** @var Driver $signature */
        $signature = app(data_get($c, 'class'));

        $signature->setConfig(Arr::except($c, ['class', 'resolver', 'repository']));

        if (data_get($c, 'resolver')) {
            $signature->setResolver($this->resolveResolver(data_get($c, 'resolver')));
        }

        if (data_get($c, 'repository')) {
            $signature->setRepository($this->resolveRepository(data_get($c, 'repository')));
        }

        return $signature;
    }

    private function resolveResolver($name): Resolver
    {
        $c = data_get($this->config, "resolvers.{$name}");
        if (!$c) {
            throw new \InvalidArgumentException("no $name resolver");
        }

        /** @var Resolver $resolver */
        $resolver = app(data_get($c, 'class'));
        $resolver->setConfig(Arr::except($c, 'class'));

        return $resolver;
    }

    private function resolveRepository($name): Repository
    {
        $c = data_get($this->config, "repositories.{$name}");
        if (!$c) {
            throw new \InvalidArgumentException("no $name repository");
        }

        /** @var Repository $repository */
        $repository = app(data_get($c, 'class'));
        $repository->setConfig(Arr::except($c, 'class'));

        return $repository;
    }
}