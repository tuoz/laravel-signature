<?php


namespace Hypocenter\LaravelSignature\Tests\Feature;


use Hypocenter\LaravelSignature\Middlewares\SignatureMiddleware;
use Hypocenter\LaravelSignature\SignatureServiceProvider;
use Illuminate\Contracts\Routing\Registrar;

abstract class SignatureTestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [SignatureServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);
        $app['config']->set('database.default', 'testing');
        $app['config']->set('signature.signatures.default.cache_driver', 'array');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->artisan('migrate:refresh', ['--database' => 'testing']);
        $this->withFactories(__DIR__ . '/../../database/factories');
    }

    protected function setUpRoute($signatureName = null): \Illuminate\Routing\Route
    {
        $signatureName = $signatureName ?: 'default';
        /** @var Registrar $router */
        $router = $this->app->make(Registrar::class);
        $route = $router->post("/$signatureName/foo", static function () {
            return 'bar';
        });
        if ($signatureName) {
            $route->middleware(SignatureMiddleware::class . ':' . $signatureName);
        } else {
            $route->middleware(SignatureMiddleware::class);
        }

        return $route;
    }

    protected function setUpCustomSignatureConfig($config): void
    {
        $config['cache_driver'] = 'array';
        $this->app->get('config')->set('signature.signatures.custom', $config);
    }
}