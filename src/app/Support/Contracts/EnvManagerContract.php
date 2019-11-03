<?php

namespace Russsiq\EnvManager\Support\Contracts;

interface EnvManagerContract
{
    /**
     * Получить полный путь к файлу окружения.
     *
     * @return string
     */
    public function filePath(): string;

    /**
     * Проверить физическое существование файла окружения.
     *
     * @return bool
     */
    public function fileExists(): bool;

    /**
     * Проверить существование значения для указанной переменной окружения.
     *
     * @param string $name Имя переменной.
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * Получить значение для указанной переменной окружения.
     *
     * @param  string      $name    Имя переменной.
     * @param  mixed       $default Значение по умолчанию.
     * @return string|null
     */
    public function get(string $name, $default = null);

    /**
     * Установить значение для переменной окружения.
     *
     * @param string      $name  Имя переменной.
     * @param string|null $value Значение переменной.
     *
     * @return self
     */
    public function set(string $name, $value = null): self;

    /**
     * Установить значения для переменных окружения.
     *
     * @param array $data  Массив из имен и значений.
     *
     * @return self
     */
    public function setMany(array $data): self;

    /**
     * Сохранить файл окружения.
     *
     * @return mixed
     */
    public function save(): bool;

    /**
     * Создать файл окружения.
     *
     * @return self
     */
    public function create(): self;
}
