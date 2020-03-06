<?php

namespace Russsiq\EnvManager;

// Сторонние зависимости.
use Illuminate\Support\ServiceProvider;
use Russsiq\EnvManager\Support\EnvManager;

class EnvManagerServiceProvider extends ServiceProvider
{
    /**
     * Все синглтоны (одиночки) контейнера,
     * которые должны быть зарегистрированы.
     * @var array
     */
    public $singletons = [
        'env-manager' => EnvManager::class,

    ];
}
