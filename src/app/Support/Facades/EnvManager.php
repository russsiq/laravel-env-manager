<?php

namespace Russsiq\EnvManager\Support\Facades;

use Illuminate\Support\Facades\Facade;

class EnvManager extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'env-manager';
    }
}
