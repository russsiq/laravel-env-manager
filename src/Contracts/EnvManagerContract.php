<?php

namespace Russsiq\EnvManager\Contracts;

/**
 * Контракт публичных методов Менеджера файла переменных окружения.
 * @var interface
 */
interface EnvManagerContract
{
    /**
     * Получить полный путь к текущему файлу окружения.
     * @return string
     */
    public function filePath(): string;

    /**
     * Установить полный путь к текущему файлу окружения.
     * @param  string  $filePath
     * @return self
     */
    public function setFilePath(string $filePath): self;

    /**
     * Сбросить полный путь к текущему файлу окружения.
     * @return self
     */
    public function resetFilePath(): self;

    /**
     * Проверить физическое существование текущего файла окружения.
     * @return bool
     */
    public function fileExists(): bool;

    /**
     * Проверить существование значения для указанной переменной окружения.
     * @param  string  $name  Имя переменной.
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Получить значение для указанной переменной окружения.
     * @param  string  $name  Имя переменной.
     * @param  mixed  $default  Значение по умолчанию.
     * @return string|null
     */
    public function get(string $name, $default = null): ?string;

    /**
     * Установить значение для переменной окружения.
     * @param  string  $name  Имя переменной.
     * @param  mixed  $value  Значение переменной.
     * @return self
     */
    public function set(string $name, $value): self;

    /**
     * Установить значения для переменных окружения.
     * @param  array  $data  Массив из имен и значений.
     * @return self
     */
    public function setMany(array $data): self;

    /**
     * Сохранить файл окружения.
     * @return bool
     */
    public function save(): bool;

    /**
     * Создать файл окружения путем копирования
     * содержимого файла по указанному полному пути.
     * @param  string  $filePath  Полный путь к исходному файлу.
     * @param  boolean  $withAppKey  Создать новый ключ приложения.
     * @return self
     *
     * @NB  Полная перезагрузка переменных окружения.
     */
    public function newFromPath(string $filePath, bool $withAppKey = false): self;
}
