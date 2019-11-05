<?php

namespace Russsiq\EnvManager\Support;

use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;

use Russsiq\EnvManager\Support\Contracts\EnvManagerContract;
use Russsiq\EnvManager\Support\Exceptions\NothingToSave;
use Russsiq\EnvManager\Support\Exceptions\UnableToRead;
use Russsiq\EnvManager\Support\Exceptions\UnableToWrite;

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
     * @param  Application  $app
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
        // `is_file` — возвращает TRUE, если файл существует и
        // является обычным файлом, иначе возвращает FALSE.
        return is_file($this->filePath());
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
     *
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
     * Сохранить файл окружения.
     *
     * @return mixed
     *
     * @throws NothingToSave При попытке сохранить пустую коллекцию.
     *
     * @see https://laravel.com/docs/5.8/upgrade#environment-variable-parsing
     * @see https://www.php.net/manual/ru/function.parse-ini-file.php#refsect1-function.parse-ini-file-notes
     */
    public function save(): bool
    {
        // Все имена переменных окружения имеют синтаксис: `SOME_VAR`.
        $collection = $this->variables->filter(function ($value, $key) {
            return str_contains($key, ['_']) and $key === mb_strtoupper($key, 'UTF-8');
        });

        if ($collection->isNotEmpty()) {
            $content = $collection->transform(function ($value, $key) {
                    // Если значение не пустое.
                    if ($value) {
                        // Если значение содержит прочие символы, кроме букв и цифр,
                        // оно должно заключаться в двойные кавычки.
                        $value = preg_match('/^[a-zA-Z0-9]+$/', $value) ? $value : "\"$value\"";
                    }

                    return $key.'='.$value;
                })
                ->values()
                ->sort()
                ->implode(PHP_EOL);

            return $this->saveContent($content);
        }

        throw new NothingToSave($this->filePath());
    }

    /**
     * Создать файл окружения путем копирования
     * содержимого файла по указанному полному пути.
     * @NB Полная перезагрузка переменных окружения.
     *
     * @param  string $filePath Полный путь к исходному файлу.
     *
     * @return self
     */
    public function newFromPath(string $filePath): EnvManagerContract
    {
        $this->filePath = $filePath;
        $this->variables = $this->getVariables();

        return $this;
    }

    /**
     * Записать данные в файл.
     *
     * @param  string $сontent Строка для записи
     * @return bool
     *
     * @throws UnableToWrite При ошибках записи файла.
     */
    protected function saveContent(string $сontent): bool
    {
        $result = file_put_contents($this->filePath(), $сontent.PHP_EOL, true);

        if (is_int($result)) {
            return true;
        }

        throw new UnableToWrite($this->filePath());
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
     * @throws UnableToRead При ошибках чтения файла.
     */
    protected function getContent(): array
    {
        // В случае ошибки синтаксиса, данная функция вернет FALSE, а не пустой массив.
        $result = parse_ini_file($this->filePath(), false, INI_SCANNER_RAW);

        if (is_array($result)) {
            return $result;
        }

        throw new UnableToRead($this->filePath());
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
