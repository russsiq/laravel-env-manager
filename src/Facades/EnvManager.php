<?php

namespace Russsiq\EnvManager\Facades;

use Illuminate\Support\Facades\Facade;
use Russsiq\EnvManager\Contracts\EnvManager as EnvManagerContract;

/**
 * @method static string filePath();
 * @method static \Russsiq\EnvManager\Contracts\EnvManager setFilePath(string $filePath);
 * @method static \Russsiq\EnvManager\Contracts\EnvManager resetFilePath();
 * @method static bool fileExists();
 * @method static bool has(string $name);
 * @method static string|null get(string $name, $default = null);
 * @method static \Russsiq\EnvManager\Contracts\EnvManager set(string $name, $value);
 * @method static \Russsiq\EnvManager\Contracts\EnvManager setMany(array $data);
 * @method static bool save();
 * @method static \Russsiq\EnvManager\Contracts\EnvManager newFromPath(string $filePath, bool $withAppKey = false);
 *
 * @see \Russsiq\EnvManager\Contracts\EnvManager
 * @see \Russsiq\EnvManager\Support\LaravelCollectionEnvManager
 */
class EnvManager extends Facade
{
    /**
     * Получить зарегистрированное имя компонента.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return EnvManagerContract::class;
    }
}
