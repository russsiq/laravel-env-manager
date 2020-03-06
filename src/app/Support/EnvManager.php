<?php

namespace Russsiq\EnvManager\Support;

// Сторонние зависимости.
use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Russsiq\EnvManager\Contracts\EnvManagerContract;
use Russsiq\EnvManager\Exceptions\NothingToSave;
use Russsiq\EnvManager\Exceptions\UnableToRead;
use Russsiq\EnvManager\Exceptions\UnableToWrite;

/**
 * Менеджер файла переменных окружения.
 */
class EnvManager implements EnvManagerContract
{
    /**
     * Экземпляр приложения.
     * Контейнер не подошел.
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
     * Создать новый экземпляр Менеджера файла переменных окружения.
     * @param  Application  $app
     */
    public function __construct(
        Application $app
    ) {
        $this->app = $app;
        $this->filePath = $this->app->environmentFilePath();
        $this->variables = $this->loadVariables();
    }

    /**
     * Получить полный путь к текущему файлу окружения.
     * @return string
     */
    public function filePath(): string
    {
        return $this->filePath;
    }

    /**
     * Проверить физическое существование текущего файла окружения.
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
     * @param  string  $name  Имя переменной.
     * @return bool
     */
    public function has(string $name): bool
    {
        return $this->variables->has($name);
    }

    /**
     * Получить значение для указанной переменной окружения.
     * @param  string  $name  Имя переменной.
     * @param  mixed  $default  Значение по умолчанию.
     * @return string|null
     */
    public function get(string $name, $default = null): ?string
    {
        return $this->variables->get($name, $default);
    }

    /**
     * Установить значение для переменной окружения.
     * @param  string  $name  Имя переменной.
     * @param  mixed  $value  Значение переменной.
     * @return self
     */
    public function set(string $name, $value): EnvManagerContract
    {
        $this->variables->put($name, $value);

        return $this;
    }

    /**
     * Установить значения для переменных окружения.
     * @param  array  $data  Массив из имен и значений.
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
     * @return bool
     *
     * @see https://laravel.com/docs/5.8/upgrade#environment-variable-parsing
     * @see https://www.php.net/manual/ru/function.parse-ini-file.php#refsect1-function.parse-ini-file-notes
     *
     * @TODO  В качесте значения может быть только строка.
     *        Если нельзя привести к строке, то отфильтровываем это значение.
     */
    public function save(): bool
    {
        // Все имена переменных окружения должны иметь синтаксис: `SOME_VAR`.
        $content = $this->variables->filter(function ($value, $key) {
                return str_contains($key, ['_']) and $key === mb_strtoupper($key, 'UTF-8');
            })
            ->transform(function ($value, $key) {
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

        return $this->assertContentIsNotEmpty($content)
            ?: $this->saveContent($content);
    }

    /**
     * Создать файл окружения путем копирования
     * содержимого файла по указанному полному пути.
     * @param  string  $filePath  Полный путь к исходному файлу.
     * @param  boolean  $withAppKey  Создать новый ключ приложения.
     * @return self
     *
     * @NB  Полная перезагрузка переменных окружения.
     */
    public function newFromPath(string $filePath, bool $withAppKey = false): EnvManagerContract
    {
        $this->filePath = $filePath;
        $this->variables = $this->loadVariables();

        return $withAppKey ? $this->set('APP_KEY', $this->generateRandomKey()) : $this;
    }

    /**
     * Записать данные в файл.
     * @param  string  $сontent  Строка для записи
     * @return bool
     */
    protected function saveContent(string $сontent): bool
    {
        // Перед сохранением содержимого файла переключаемся на корневой файл.
        $this->filePath = $this->app->environmentFilePath();

        $result = file_put_contents($this->filePath(), $сontent.PHP_EOL, LOCK_EX);

        return $this->assertFileWriteIsSuccessful($result) ?: true;
    }

    /**
     * Получить содержимое файла окружения.
     * @return Collection
     */
    protected function loadVariables(): Collection
    {
        return collect($this->fileExists() ? $this->getContent() : []);
    }

    /**
     * Получить содержимое файла окружения.
     * @return array
     */
    protected function getContent(): array
    {
        $result = parse_ini_file($this->filePath(), false, INI_SCANNER_RAW);

        return $this->assertFileParseIsSuccessful($result) ?: $result;
    }

    /**
     * Сгенерировать случайный ключ для приложения.
     * @return string
     *
     * @see \Illuminate\Foundation\Console\KeyGenerateCommand
     */
    protected function generateRandomKey(): string
    {
        return 'base64:'.base64_encode(
            Encrypter::generateKey($this->app['config']['app.cipher'])
        );
    }

    /**
     * Определить, что предоставленное содержимое не пустая строка.
     * @param  string  $content
     * @return void
     *
     * @throws NothingToSave
     */
    protected function assertContentIsNotEmpty($content): void
    {
        if (! is_string($content) || '' === $content) {
            throw new NothingToSave($this->filePath());
        }
    }

    /**
     * Подтвердить успешность записи содержимого в файл
     * с использованием функции `file_put_contents`, которая
     * возвращает количество записанных байт в файл,
     * или FALSE в случае ошибки.
     * @param  mixed  $result
     * @return void
     *
     * @throws NothingToSave
     */
    protected function assertFileWriteIsSuccessful($result): void
    {
        if (false === $result) {
            throw new UnableToWrite($this->filePath());
        }
    }

    /**
     * Подтвердить успешность парсинга содержимого файла
     * с использованием функции `parse_ini_file`, которая
     * в случае успеха возвращает настройки
     * в виде ассоциативного массива (array),
     * а в случае ошибки возвращает FALSE.
     * @param  mixed  $result
     * @return void
     *
     * @throws NothingToSave
     */
    protected function assertFileParseIsSuccessful($result): void
    {
        if (false === $result) {
            throw new UnableToRead($this->filePath());
        }
    }
}
