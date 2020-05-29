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
    private const DUMMY_DIR = __DIR__.'/tmp';
    private const DUMMY_CIPHER = 'AES-256-CBC';

    /**
     * Этот метод вызывается перед запуском
     * первого теста этого класса тестирования.
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        mkdir(self::DUMMY_DIR);
    }

    /**
     * Этот метод вызывается перед каждым тестом.
     * @return void
     */
    protected function setUp(): void
    {
    }

    /**
     * Этот метод вызывается после каждого теста.
     * @return void
     */
    protected function tearDown(): void
    {
    }

    /**
     * Этот метод вызывается после запуска
     * последнего теста этого класса тестирования.
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        rmdir(self::DUMMY_DIR);
    }

    /**
     * Экземпляр менеджера успешно создан.
     * @return EnvManagerContract
     */
    public function testSuccessfullyInitiated(): EnvManagerContract
    {
        $manager = new EnvManager(self::DUMMY_DIR, self::DUMMY_CIPHER);

        $this->assertInstanceOf(EnvManagerContract::class, $manager);

        return $manager;
    }
}
