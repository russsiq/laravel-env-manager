<?php

namespace Russsiq\EnvManager;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Russsiq\EnvManager\Support\EnvManager;

class EnvManagerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('env-manager', function (Application $app) {
            return new EnvManager(
                $app->environmentFilePath(),
                $app->config->get('app.cipher')
            );
        });
    }
}
