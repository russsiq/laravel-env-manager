<?php

namespace Russsiq\EnvManager\Facades;

use Illuminate\Support\Facades\Facade;
use Russsiq\EnvManager\Contracts\EnvManagerContract;

/**
 * @method static string filePath();
 * @method static \Russsiq\EnvManager\Contracts\EnvManagerContract setFilePath(string $filePath);
 * @method static \Russsiq\EnvManager\Contracts\EnvManagerContract resetFilePath();
 * @method static bool fileExists();
 * @method static bool has(string $name);
 * @method static string|null get(string $name, $default = null);
 * @method static \Russsiq\EnvManager\Contracts\EnvManagerContract set(string $name, $value);
 * @method static \Russsiq\EnvManager\Contracts\EnvManagerContract setMany(array $data);
 * @method static bool save();
 * @method static \Russsiq\EnvManager\Contracts\EnvManagerContract newFromPath(string $filePath, bool $withAppKey = false);
 *
 * @see \Russsiq\EnvManager\Contracts\EnvManagerContract
 * @see \Russsiq\EnvManager\Support\EnvManager
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
