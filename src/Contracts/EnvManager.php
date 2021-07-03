<?php

namespace Russsiq\EnvManager\Contracts;

/**
 * Контракт публичных методов Менеджера файла переменных окружения.
 *
 * @var interface
 */
interface EnvManager
{
    /**
     * Регулярка для проверки имени ключа перед сохранением.
     * Ключ должен начинаться с буквы; кроме букв и цифр
     * может содержать, как правило, нижнее подчеркивание.
     * > Сохранение строк комментариев не допускается.
     *
     * @const string
     */
    public const REGEX_VALID_KEY = '/\A[A-Z]{1}[A-Z0-9\_]+\z/';

    /**
     * Регулярка допустимых значений.
     * Если значение в файле содержит прочие символы,
     * кроме букв и цифр, оно должно заключаться в двойные кавычки.
     *
     * @const string
     */
    public const REGEX_ACCEPTABLE_VALUE = '/\A[a-zA-Z0-9]+\z/';

    /**
     * Получить полный путь к текущему файлу окружения.
     *
     * @return string
     */
    public function filePath(): string;

    /**
     * Установить полный путь к текущему файлу окружения.
     *
     * @param  string  $filePath
     *
     * @return self
     */
    public function setFilePath(string $filePath): self;

    /**
     * Сбросить полный путь к текущему файлу окружения.
     *
     * @return self
     */
    public function resetFilePath(): self;

    /**
     * Проверить физическое существование текущего файла окружения.
     *
     * @return bool
     */
    public function fileExists(): bool;

    /**
     * Проверить существование значения для указанной переменной окружения.
     *
     * @param  string  $name  Имя переменной.
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Получить значение для указанной переменной окружения.
     *
     * @param  string  $name  Имя переменной.
     * @param  mixed  $default  Значение по умолчанию.
     *
     * @return string|null
     */
    public function get(string $name, $default = null): ?string;

    /**
     * Установить значение для переменной окружения.
     *
     * @param  string  $name  Имя переменной.
     * @param  mixed  $value  Значение переменной.
     *
     * @return self
     */
    public function set(string $name, $value): self;

    /**
     * Установить значения для переменных окружения.
     *
     * @param  array  $data  Массив из имен и значений.
     *
     * @return self
     */
    public function setMany(array $data): self;

    /**
     * Сохранить файл окружения.
     *
     * @return bool
     */
    public function save(): bool;

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
    public function newFromPath(string $filePath): self;

    /**
     * Создать новый ключ приложения.
     *
     * @return self
     */
    public function withNewAppKey(): self;
}
