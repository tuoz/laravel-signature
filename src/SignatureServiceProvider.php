<?php


namespace Hypocenter\LaravelSignature;


use Hypocenter\LaravelSignature\Contracts\Factory;
use Illuminate\Support\ServiceProvider;

class SignatureServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/signature.php', 'signature'
        );

        $this->registerSignatureManager();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/signature.php' => config_path('signature.php'),
            ], 'signature-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'signature-migrations');

            $this->publishes([
                __DIR__ . '/../database/factories' => database_path('factories'),
            ], 'signature-factories');
        }
    }

    public function provides(): array
    {
        return ['signature'];
    }

    private function registerSignatureManager(): void
    {
        $this->app->singleton('signature', static function ($app) {
            $config = $app['config']->get('signature');
            return new SignatureManager($config, $app);
        });

        $this->app->alias('signature', Factory::class);
    }
}