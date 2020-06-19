<?php

namespace Russsiq\EnvManager;

// Сторонние зависимости.
use Illuminate\Support\ServiceProvider;
use Russsiq\EnvManager\Support\EnvManager;

class EnvManagerServiceProvider extends ServiceProvider
{
    /**
     * Регистрация Менеджера файла переменных окружения.
     * @return void
     */
    public function register()
    {
        $this->app->singleton('env-manager', function ($app) {
            return new EnvManager(
                $app->environmentFilePath(),
                $app->config->get('app.cipher')
            );
        });
    }
}
