<?php

namespace Russsiq\EnvManager\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Russsiq\EnvManager\Support\Contracts\EnvManagerContract
 * @see \Russsiq\EnvManager\Support\EnvManager
 */
class EnvManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'env-manager';
    }
}
