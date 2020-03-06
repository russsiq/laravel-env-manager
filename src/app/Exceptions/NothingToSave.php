<?php

namespace Russsiq\EnvManager\Exceptions;

use RuntimeException;

class NothingToSave extends RuntimeException
{
    /**
     * Полный путь к файлу.
     *
     * @var string
     */
    protected $filePath;

    /**
     * Сообщение Исключения.
     *
     * @var string
     */
    protected $message = 'The data is not available for saving to a file.';

    /**
     * Создать новый экземпляр Исключения.
     *
     * @param string $filePath Полный путь к файлу.
     */
    public function __construct(string $filePath)
    {
        parent::__construct($this->message);

        $this->filePath = $filePath;
    }

    /**
     * Получить полный путь к файлу.
     *
     * @return string
     */
    public function filePath(): string
    {
        return $this->filePath;
    }
}
