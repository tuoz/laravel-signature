<?php


namespace Hypocenter\LaravelSignature;


use Hypocenter\LaravelSignature\Contracts\Factory;
use Illuminate\Support\ServiceProvider;

class SignatureServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/signature.php', 'signature'
        );

        $this->app->singleton('signature', function ($app) {
            $config = $app->make('config')->get('signature');
            return new SignatureManager($config);
        });

        $this->app->alias('signature', Factory::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/signature.php' => config_path('signature.php'),
        ]);
    }

    public function provides()
    {
        return ['signature'];
    }
}