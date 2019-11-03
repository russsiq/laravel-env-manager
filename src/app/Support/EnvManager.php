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
     * Полный путь к файлу окружения.
     * @var string
     */
    protected $filePath;

    /**
     * Коллекция текущих переменных.
     * @var Collection
     */
    protected $variables;

    /**
     * Создать новый экземпляр менеджера файла переменных окружения.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->filePath = $this->app->environmentFilePath();
        $this->variables = $this->getVariables();
    }

    /**
     * Получить полный путь к файлу окружения.
     *
     * @return string
     */
    public function filePath(): string
    {
        return $this->filePath;
    }

    /**
     * Проверить физическое существование файла окружения.
     *
     * @return bool
     */
    public function fileExists(): bool
    {
        return File::isFile($this->filePath());
    }

    /**
     * Сгенерировать случайный ключ для приложения.
     * По мотивам: `\Illuminate\Foundation\Console\KeyGenerateCommand`.
     *
     * @return string
     */
    protected function generateRandomKey(): string
    {
        return 'base64:'.base64_encode(
            Encrypter::generateKey($this->app['config']['app.cipher'])
        );
    }
}
