<?php

namespace Russsiq\EnvManager;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

use Russsiq\EnvManager\Support\EnvManager;

class EnvManagerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('env-manager', function (Container $app) {
            return new EnvManager($app);
        });
    }

    public function provides()
    {
        return [
            'env-manager',
        ];
    }
}
