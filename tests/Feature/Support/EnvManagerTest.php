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
     * Экземпляр менеджера.
     * @var EnvManager
     */
    private $manager;

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
        $this->manager = new EnvManager(
            $this->environmentFilePath = self::DUMMY_DIR.'/.env',
            $this->cipher = self::DUMMY_CIPHER
        );
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
     * @test
     * @cover ::__construct
     *
     * Экземпляр менеджера успешно создан.
     * @return void
     */
    public function testSuccessfullyInitiated(): void
    {
        $this->assertInstanceOf(EnvManagerContract::class, $this->manager);
    }

    /**
     * @test
     * @cover ::filePath
     *
     * Подтвердить актуальность файла окружения.
     * @return void
     */
    public function testFilePath(): void
    {
        $filePath = $this->manager->filePath();

        $this->assertIsString($filePath);
        $this->assertNotEmpty($filePath);
        $this->assertEquals($this->environmentFilePath, $filePath);
    }

    /**
     * @test
     * @cover ::setFilePath
     *
     * Подтвердить успешность задания полного пути
     * текущего файла окружения.
     * @return void
     */
    public function testSetFilePath(): void
    {
        $wrongPath = 'dummy/path/.env';

        $this->manager->setFilePath($wrongPath);

        $this->assertEquals($wrongPath, $this->manager->filePath());
    }

    /**
     * @test
     * @cover ::resetFilePath
     * @depends testSetFilePath
     *
     * Подтвердить успешность сброса полного пути
     * текущего файла окружения.
     * @return void
     */
    public function testResetFilePath(): void
    {
        $this->manager->resetFilePath();

        $this->assertEquals($this->environmentFilePath, $this->manager->filePath());
    }

    /**
     * @test
     * @cover ::fileExists
     *
     * Подтвердить физическое присутствие файла окружения.
     * @return void
     */
    public function testFileExists(): void
    {
        $filePath = $this->manager->filePath();

        // Перед проверкой существования файла
        // создадим его.
        file_put_contents($filePath, 'dummy content', LOCK_EX);

        $this->assertFileExists($filePath);
        $this->assertTrue($this->manager->fileExists());
        $this->assertEquals($filePath, $this->environmentFilePath);

        // Удалим временный файл, что необходимо для зависимого теста.
        unlink($this->manager->filePath());
    }

    /**
     * @test
     * @cover ::fileExists
     * @depends testFileExists
     *
     * Подтвердить физическое отсутствие файла окружения.
     * @return void
     */
    public function testFileNotExists(): void
    {
        $this->assertFalse($this->manager->fileExists());
    }
}
