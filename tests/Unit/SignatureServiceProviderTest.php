<?php

namespace Hypocenter\LaravelSignature\Tests\Unit;

use Hypocenter\LaravelSignature\Contracts\Factory;
use Hypocenter\LaravelSignature\SignatureManager;
use Hypocenter\LaravelSignature\SignatureServiceProvider;
use Orchestra\Testbench\TestCase;


class SignatureServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [SignatureServiceProvider::class];
    }

    public function testRegisterSignatureManager(): void
    {
        $this->assertInstanceOf(SignatureManager::class, $this->app->make('signature'));
        $this->assertInstanceOf(SignatureManager::class, $this->app->make(Factory::class));
    }

    public function testConfigIsLoaded(): void
    {
        $this->assertNotEmpty($this->app['config']['signature']);
    }
}