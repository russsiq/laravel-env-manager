<?php

namespace Russsiq\EnvManager;

use Russsiq\EnvManager\Support\EnvManager;

use Illuminate\Support\ServiceProvider;

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
        $this->app->singleton('env-manager', function ($app) {
            return new EnvManager();
        });
    }

    public function provides()
    {
        return [
            'env-manager',
        ];
    }
}
