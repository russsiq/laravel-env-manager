<?php

namespace Russsiq\EnvManager\Support;

use Illuminate\Foundation\Application;

use Russsiq\EnvManager\Support\Contracts\EnvManagerContract;

class EnvManager implements EnvManagerContract
{
    /**
     * Экземпляр приложения.
     *
     * @var Application
     */
    protected $app;

    /**
     * Создать новый экземпляр менеджера файла переменных окружения.
     * 
     * @param  Application  $app
     */
	public function __construct(Application $app)
	{
		$this->app = $app;
	}
}
