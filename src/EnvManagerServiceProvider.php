<?php

namespace Russsiq\EnvManager;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Russsiq\EnvManager\Contracts\EnvManager;
use Russsiq\EnvManager\Support\LaravelCollectionEnvManager;

class EnvManagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            EnvManager::class,
            function (Application $app) {
                return new LaravelCollectionEnvManager(
                    $app->environmentFilePath(),
                    $app->config->get('app.cipher')
                );
            }
        );
    }
}
