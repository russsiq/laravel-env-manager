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
     * Полный путь к файлу окружения приложения.
     * @var string
     */
    private $environmentFilePath;

    /**
     * Алгоритм, используемый для шифрования.
     * @var string
     */
    private $cipher;

    /**
     * Этот метод вызывается перед запуском
     * первого теста этого класса тестирования.
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        $directory = self::DUMMY_DIR;

        if (!is_dir($directory)) {
            mkdir($directory);
        }
    }

    /**
     * Этот метод вызывается перед каждым тестом.
     * @return void
     */
    protected function setUp(): void
    {
        $this->environmentFilePath = self::DUMMY_DIR.'/.env';
        $this->cipher = self::DUMMY_CIPHER;
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
        $directory = self::DUMMY_DIR;

        if (is_dir($directory)) {
            rmdir($directory);
        }
    }

    /**
     * Экземпляр менеджера успешно создан.
     * Имитация работы поставщика службы.
     * @return EnvManagerContract
     */
    public function testSuccessfullyInitiated(): EnvManagerContract
    {
        $manager = new EnvManager($this->environmentFilePath, $this->cipher);

        $this->assertInstanceOf(EnvManagerContract::class, $manager);

        return $manager;
    }

    /**
     * @test
     * @cover ::fileExists
     * @depends testSuccessfullyInitiated
     *
     * Подтвердить физическое присутствие файла окружения.
     * @param  EnvManagerContract  $manager
     * @return void
     */
    public function testFileExists(EnvManagerContract $manager): void
    {
        file_put_contents($this->environmentFilePath, 'dummy content', LOCK_EX);

        $this->assertTrue($manager->fileExists());

        unlink($this->environmentFilePath);
    }

    /**
     * @test
     * @cover ::fileExists
     * @depends testSuccessfullyInitiated
     *
     * Подтвердить физическое отсутствие файла окружения.
     * @param  EnvManagerContract  $manager
     * @return void
     */
    public function testFileNotExists(EnvManagerContract $manager): void
    {
        $this->assertFalse($manager->fileExists());
    }
}
