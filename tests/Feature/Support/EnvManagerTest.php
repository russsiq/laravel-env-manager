<?php declare(strict_types=1);

namespace Tests\Feature\Support;

// Тестируемый класс.
use Russsiq\EnvManager\Support\EnvManager;

// Базовые расширения PHP.

// Сторонние зависимости.
use Illuminate\Encryption\Encrypter;
use PHPUnit\Framework\TestCase;
use Russsiq\EnvManager\Contracts\EnvManagerContract;

/**
 * @coversDefaultClass \Russsiq\EnvManager\Support\EnvManager
 */
class EnvManagerTest extends TestCase
{
    public function testExample(): void
    {
        $this->assertTrue(true);
    }
}
