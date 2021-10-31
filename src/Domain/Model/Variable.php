<?php

declare(strict_types=1);

namespace Russsiq\EnvManager\Domain\Model;

use Russsiq\EnvManager\Domain\Model\VariableException;
use Stringable;
use function addcslashes;
use function preg_match;
use function sprintf;
use function trim;

class Variable implements Stringable
{
    /**
     * Ключ *Переменной окружения*.
     *
     * @var string
     */
    private string $key;

    /**
     * Значение *Переменной окружения*.
     *
     * @var string
     */
    private string $value;

    /**
     * Регулярное выражение для проверки имени ключа перед сохранением.
     *
     * Ключ должен начинаться с буквы; кроме букв и цифр
     * может содержать, как правило, нижнее подчеркивание.
     *
     * > Сохранение строк комментариев не допускается.
     *
     * @const string
     */
    private const REGEX_VALID_KEY_FORMAT = '/\A[A-Z]{1}[A-Z0-9\_]+\z/';

    /**
     * Регулярное выражение допустимых значений.
     *
     * Если значение в файле содержит прочие символы,
     * кроме букв и цифр, оно должно заключаться в двойные кавычки.
     *
     * @const string
     */
    private const REGEX_ACCEPTABLE_VALUE = '/\A[a-zA-Z0-9]*\z/';

    /**
     * Создать новую *Переменную окружения* с пустым значением.
     *
     * @param  string  $key
     *
     * @return self
     */
    public static function createWithEmptyValue(string $key): self
    {
        return new self($key, '');
    }

    /**
     * Создать экземпляр *Переменной окружения*.
     *
     * @param  string  $key
     * @param  string  $value
     *
     * @return void
     */
    public function __construct(string $key, string $value)
    {
        $this->setKey($key);
        $this->setValue($value);
    }

    /**
     * Задать ключ *Переменной окружения*.
     *
     * @param  string  $key
     *
     * @return void
     */
    private function setKey(string $key): void
    {
        $this->key = $this->prepareKey($key);
    }

    /**
     * Подготовить ключ *Переменной окружения*.
     *
     * @param  string  $key
     *
     * @return string
     */
    private function prepareKey(string $key): string
    {
        // Обрежем пустоты, переносы, табуляцию.
        $key = trim($key);

        $this->assertKeyIsValid($key);

        return $key;
    }

    /**
     * Задать значение *Переменной окружения*.
     *
     * @param  string  $value
     *
     * @return void
     */
    private function setValue(string $value): void
    {
        $this->value = $this->prepareValue($value);
    }

    /**
     * Подготовить значение *Переменной окружения*.
     *
     * @param  string  $value
     *
     * @return string
     */
    private function prepareValue(string $value): string
    {
        // Обрежем пустоты, переносы, табуляцию.
        $value = trim($value);

        $this->assertValueIsValid($value);

        // Если значение не пустое и оно не является допустимым,
        // то его необходимо экранировать.
        if (! preg_match(self::REGEX_ACCEPTABLE_VALUE, $value)) {
            $value = '"'.addcslashes($value, '"').'"';
        }

        return $value;
    }

    /**
     * Получить ключ *Переменной окружения*.
     *
     * @return string
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * Получить значение *Переменной окружения*.
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Сравнить значения *Переменных окружения*.
     *
     * @param  self  $variable
     *
     * @return bool
     */
    public function equals(self $variable): bool
    {
        return $this->value() === $variable->value();
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return sprintf(
            "%s=%s", $this->key(), $this->value()
        );
    }

    /**
     * Убедиться, что ключ *Переменной окружения* является допустимым.
     *
     * @param string $key
     *
     * @return void
     *
     * @throws VariableException
     */
    private function assertKeyIsValid(string $key): void
    {
        if (! preg_match(self::REGEX_VALID_KEY_FORMAT, $key)) {
            throw VariableException::invalidKeyFormat($key);
        }
    }

    /**
     * Убедиться, что значение *Переменной окружения* является допустимым.
     *
     * @param string $value
     *
     * @return void
     */
    private function assertValueIsValid(string $value): void
    {
        // Валидация значения ...
    }
}
