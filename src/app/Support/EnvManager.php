<?php

namespace Russsiq\EnvManager\Support;

use RuntimeException;

use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

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
     * Проверить существование значения для указанной переменной окружения.
     *
     * @param string $name Имя переменной.
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return $this->variables->has($name);
    }

    /**
     * Получить значение для указанной переменной окружения.
     *
     * @param  string $name    Имя переменной.
     * @param  mixed  $default Значение по умолчанию.
     * @return string
     */
    public function get(string $name, $default = null)
    {
        return $this->variables->get($name, $default);
    }

    /**
     * Установить значение для переменной окружения.
     *
     * @param string      $name  Имя переменной.
     * @param string|null $value Значение переменной.
     *
     * @return self
     */
    public function set(string $name, $value = null): EnvManagerContract
    {
        $this->variables->put($name, $value);

        return $this;
    }

    /**
     * Установить значения для переменных окружения.
     *
     * @param array $data  Массив из имен и значений.
     *
     * @return self
     */
    public function setMany(array $data): EnvManagerContract
    {
        foreach ($data as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    /**
     * Получить содержимое файла окружения.
     *
     * @return Collection
     */
    protected function getVariables(): Collection
    {
        return collect($this->fileExists() ? $this->getContent() : []);
    }

    /**
     * Получить содержимое файла окружения.
     *
     * @return array
     *
     * @throws RuntimeException
     */
    protected function getContent(): array
    {
        $result = parse_ini_file($this->filePath(), false, INI_SCANNER_RAW);

        if (is_array($result)) {
            return $result;
        }

        throw new RuntimeException('Unable to read the environment file.');
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
