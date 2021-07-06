<?php

namespace Russsiq\EnvManager\Support;

use Illuminate\Support\Collection;
use Russsiq\EnvManager\Contracts\EnvManager;
use Russsiq\EnvManager\Exceptions\NothingToSave;
use Russsiq\EnvManager\Exceptions\UnableToRead;
use Russsiq\EnvManager\Exceptions\UnableToWrite;

/**
 * Менеджер файла переменных окружения.
 */
class LaravelCollectionEnvManager implements EnvManager
{
    /**
     * Полный путь к файлу окружения приложения.
     *
     * @var string
     */
    protected $environmentFilePath;

    /**
     * Алгоритм, используемый для шифрования.
     *
     * @var string
     */
    protected $cipher;

    /**
     * Полный путь к текущему файлу окружения.
     *
     * @var string
     */
    protected $filePath;

    /**
     * Коллекция текущих переменных.
     *
     * @var Collection
     */
    protected $variables;

    /**
     * Создать новый экземпляр Менеджера файла переменных окружения.
     *
     * @param string $environmentFilePath
     * @param string $cipher
     */
    public function __construct(
        string $environmentFilePath,
        string $cipher
    ) {
        $this->environmentFilePath = $environmentFilePath;
        $this->cipher = $cipher;

        $this->variables = $this->resetFilePath()
            ->loadVariables();
    }

    /**
     * Получить полный путь к текущему файлу окружения.
     *
     * @return string
     */
    public function filePath(): string
    {
        return $this->filePath;
    }

    /**
     * Установить полный путь к текущему файлу окружения.
     *
     * @param  string  $filePath
     *
     * @return self
     */
    public function setFilePath(string $filePath): EnvManager
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Сбросить полный путь к текущему файлу окружения.
     *
     * @return self
     */
    public function resetFilePath(): EnvManager
    {
        $this->filePath = $this->environmentFilePath;

        return $this;
    }

    /**
     * Проверить физическое существование текущего файла окружения.
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
     * @param  string  $name  Имя переменной.
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
     * @param  string  $name  Имя переменной.
     * @param  mixed  $default  Значение по умолчанию.
     *
     * @return string|null
     */
    public function get(string $name, $default = null): ?string
    {
        return $this->variables->get($name, $default);
    }

    /**
     * Установить значение для переменной окружения.
     *
     * @param  string  $name  Имя переменной.
     * @param  mixed  $value  Значение переменной.
     *
     * @return self
     */
    public function set(string $name, $value): EnvManager
    {
        $this->variables->put($name, $value);

        return $this;
    }

    /**
     * Установить значения для переменных окружения.
     *
     * @param  array  $data  Массив из имен и значений.
     *
     * @return self
     */
    public function setMany(array $data): EnvManager
    {
        foreach ($data as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    /**
     * Сохранить файл окружения.
     *
     * @return bool
     *
     * @see https://laravel.com/docs/5.8/upgrade#environment-variable-parsing
     * @see https://www.php.net/manual/ru/function.parse-ini-file.php#refsect1-function.parse-ini-file-notes
     */
    public function save(): bool
    {
        $content = $this->variables->mapWithKeys(
            // Обрежем пустоты, переносы, табуляцию.
            function (mixed $value, mixed $key) {
                return [
                    trim($key) => trim($value)
                ];
            }
        )
        ->filter(
            // Отфильтруем пары с невалидными ключами.
            function (mixed $value, mixed $key) {
                return 1 === preg_match(self::REGEX_VALID_KEY, $key);
            }
        )
        ->transform(
            // Выполним дополнительные преобразования.
            // К этому моменту ключи должны иметь тип `string`.
            function (mixed $value, string $key) {
                // Если значение не пустое и оно не допустимо без экранирования.
                if ($value && 1 !== preg_match(self::REGEX_ACCEPTABLE_VALUE, $value)) {
                    $value = '"'.addcslashes($value, '"').'"';
                }

                return $key.'='.$value;
            }
        )
            ->values()
            ->sort()
            ->implode(PHP_EOL);

        return $this->assertContentIsNotEmpty($content)
            ?: $this->saveContent($content);
    }

    /**
     * Создать файл окружения путем копирования
     * содержимого файла по указанному полному пути.
     *
     * @param  string  $filePath  Полный путь к исходному файлу.
     *
     * @return self
     *
     * @NB  Полная перезагрузка переменных окружения.
     */
    public function newFromPath(string $filePath): EnvManager
    {
        $this->variables = $this->setFilePath($filePath)
            ->loadVariables();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withNewAppKey(): EnvManager
    {
        return $this->set('APP_KEY', $this->generateRandomKey());
    }

    /**
     * Записать данные в файл.
     *
     * @param  string  $content  Строка для записи
     *
     * @return bool
     */
    protected function saveContent(string $content): bool
    {
        // Перед сохранением содержимого файла переключаемся на корневой файл.
        $this->resetFilePath();

        $result = file_put_contents($this->filePath(), $content.PHP_EOL, LOCK_EX);

        return $this->assertFileWriteIsSuccessful($result) ?: true;
    }

    /**
     * Получить содержимое файла окружения.
     *
     * @return Collection
     */
    protected function loadVariables(): Collection
    {
        return Collection::make(
            $this->fileExists() ? $this->getContent() : []
        );
    }

    /**
     * Получить содержимое файла окружения.
     *
     * @return array
     */
    protected function getContent(): array
    {
        $result = parse_ini_file($this->filePath(), false, INI_SCANNER_RAW);

        return $this->assertFileParseIsSuccessful($result) ?: $result;
    }

    /**
     * Сгенерировать случайный ключ для приложения.
     *
     * @return string
     *
     * @see \Illuminate\Foundation\Console\KeyGenerateCommand
     */
    protected function generateRandomKey(): string
    {
        return 'base64:'.base64_encode(
            $this->generateKey($this->cipher)
        );
    }

    /**
     * Create a new encryption key for the given cipher.
     *
     * @param  string  $cipher
     * @return string
     */
    protected function generateKey(string $cipher): string
    {
        return random_bytes(
            'AES-128-CBC' === $cipher ? 16 : 32
        );
    }

    /**
     * Определить, что предоставленное содержимое не пустая строка.
     *
     * @param  string  $content
     *
     * @return void
     *
     * @throws NothingToSave
     */
    protected function assertContentIsNotEmpty($content): void
    {
        if (! is_string($content) || empty($content)) {
            throw new NothingToSave($this->filePath());
        }
    }

    /**
     * Подтвердить успешность записи содержимого в файл
     * с использованием функции `file_put_contents`, которая
     * возвращает количество записанных байт в файл,
     * или FALSE в случае ошибки.
     *
     * @param  mixed  $result
     *
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
     *
     * @param  mixed  $result
     *
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
