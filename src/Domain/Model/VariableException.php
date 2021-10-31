<?php

declare(strict_types=1);

namespace Russsiq\EnvManager\Domain\Model;

use DomainException;
use function sprintf;

class VariableException extends DomainException
{
    /**
     * Исключение об уже существующей *Переменной окружения*.
     *
     * @param  string  $key
     *
     * @return self
     */
    public static function alreadyExists(string $key): self
    {
        return new self(sprintf(
            "Variable with key [%s] already exists.",
            $key
        ));
    }

    /**
     * Исключение о недопустимом формате ключа *Переменной окружения*.
     *
     * @param  string  $key
     *
     * @return self
     */
    public static function invalidKeyFormat(string $key): self
    {
        return new self(sprintf(
            "Format key [%s] is not supported for environment variable.",
            $key
        ));
    }
}
