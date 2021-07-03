<?php

declare(strict_types=1);

namespace Tests\Feature\Support;

use PHPUnit\Framework\TestCase;
use Russsiq\EnvManager\Contracts\EnvManager;
use Russsiq\EnvManager\Support\LaravelCollectionEnvManager;

/**
 * @coversDefaultClass \Russsiq\EnvManager\Support\LaravelCollectionEnvManager
 */
class LaravelCollectionEnvManagerTest extends TestCase
{
    private const DUMMY_DIR = __DIR__.'/tmp';

    private const DUMMY_CIPHER = 'AES-256-CBC';

    /** Полный путь к файлу окружения приложения. */
    private string $environmentFilePath;

    /** Алгоритм, используемый для шифрования. */
    private string $cipher;

    /** Экземпляр менеджера. */
    private EnvManager $manager;

    /** Этот метод вызывается перед запуском первого теста этого класса тестирования. */
    public static function setUpBeforeClass(): void
    {
        // Очищаем кеш состояния файлов,
        // так как тестирование выполняется
        // на реальной файловой системе.
        clearstatcache();

        $directory = self::DUMMY_DIR;

        if (! is_dir($directory)) {
            mkdir($directory);
        }
    }

    /** Этот метод вызывается перед каждым тестом. */
    protected function setUp(): void
    {
        $this->manager = new LaravelCollectionEnvManager(
            $this->environmentFilePath = self::DUMMY_DIR.'/.env',
            $this->cipher = self::DUMMY_CIPHER
        );
    }

    /** Этот метод вызывается после каждого теста. */
    protected function tearDown(): void
    {
        // Очищаем кеш состояния файлов,
        // так как тестирование выполняется
        // на реальной файловой системе.
        clearstatcache();
    }

    /** Этот метод вызывается после запуска последнего теста этого класса тестирования. */
    public static function tearDownAfterClass(): void
    {
        $directory = self::DUMMY_DIR;

        if (is_dir($directory)) {
            rmdir($directory);
        }
    }

    /**
     * @covers ::__construct
     */
    public function test_successfully_initiated(): void
    {
        $this->assertInstanceOf(EnvManager::class, $this->manager);
    }

    /**
     * @covers ::filePath
     */
    public function test_file_path(): void
    {
        $filePath = $this->manager->filePath();

        $this->assertIsString($filePath);
        $this->assertNotEmpty($filePath);
        $this->assertEquals($this->environmentFilePath, $filePath);
    }

    /**
     * @covers ::setFilePath
     */
    public function test_set_file_path(): void
    {
        $wrongPath = 'dummy/path/.env';

        $this->manager->setFilePath($wrongPath);

        $this->assertEquals($wrongPath, $this->manager->filePath());
    }

    /**
     * @covers ::resetFilePath
     */
    public function test_reset_file_path(): void
    {
        $wrongPath = 'dummy/path/.env';

        $this->manager->setFilePath($wrongPath);

        $this->assertEquals($wrongPath, $this->manager->filePath());

        $this->manager->resetFilePath();

        $this->assertEquals($this->environmentFilePath, $this->manager->filePath());
    }

    /**
     * @covers ::fileExists
     */
    public function test_file_exists(): void
    {
        $filePath = $this->manager->filePath();

        // Перед проверкой существования файла создадим его.
        file_put_contents($filePath, $this->sampleTestingStringableContent(), LOCK_EX);

        $this->assertFileExists($filePath);
        $this->assertTrue($this->manager->fileExists());
        $this->assertEquals($filePath, $this->environmentFilePath);

        // Удалим временный файл, что необходимо для зависимого теста.
        unlink($this->manager->filePath());

        $this->assertFalse($this->manager->fileExists());
    }

    /**
     * @covers ::has
     */
    public function test_has_is_true(): void
    {
        // Перед проверкой переменных файла создадим его.
        file_put_contents($this->environmentFilePath, $this->sampleTestingStringableContent(), LOCK_EX);

        $this->manager = new LaravelCollectionEnvManager($this->environmentFilePath, $this->cipher);
        $this->assertInstanceOf(EnvManager::class, $this->manager);
        $this->assertTrue($this->manager->has('APP_NAME'));

        // По окончании проверки Удалим временный файл.
        unlink($this->manager->filePath());
    }

    /**
     * @covers ::has
     */
    public function test_has_is_false(): void
    {
        $this->assertFalse($this->manager->has('not-exist'));
    }

    /**
     * @covers ::get
     */
    public function test_get(): void
    {
        // Перед проверкой переменных файла создадим его.
        file_put_contents($this->environmentFilePath, $this->sampleTestingStringableContent(), LOCK_EX);

        $this->manager = new LaravelCollectionEnvManager($this->environmentFilePath, $this->cipher);
        $this->assertInstanceOf(EnvManager::class, $this->manager);
        $this->assertSame('Example', $this->manager->get('APP_NAME'));

        // По окончании проверки Удалим временный файл.
        unlink($this->manager->filePath());
    }

    /**
     * @covers ::get
     */
    public function test_get_with_default(): void
    {
        // Перед проверкой переменных файла создадим его.
        file_put_contents($this->environmentFilePath, $this->sampleTestingStringableContent(), LOCK_EX);

        $this->manager = new LaravelCollectionEnvManager($this->environmentFilePath, $this->cipher);
        $this->assertInstanceOf(EnvManager::class, $this->manager);
        $this->assertSame('default', $this->manager->get('not-exist', 'default'));

        // По окончании проверки Удалим временный файл.
        unlink($this->manager->filePath());
    }

    /**
     * @covers ::set
     */
    public function test_set(): void
    {
        $this->manager->set('key', 'value');

        $this->assertSame('value', $this->manager->get('key'));
    }

    /**
     * @covers ::setMany
     */
    public function test_set_many(): void
    {
        $this->manager->setMany([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $this->assertSame('value1', $this->manager->get('key1'));
        $this->assertSame('value2', $this->manager->get('key2'));
    }

    /**
     * @covers ::save
     */
    public function test_save(): void
    {
        $this->assertFalse($this->manager->fileExists());

        // Перед проверкой переменных файла создадим его.
        file_put_contents($this->environmentFilePath, $this->sampleTestingStringableContent(), LOCK_EX);

        $this->manager = new LaravelCollectionEnvManager($this->environmentFilePath, $this->cipher);
        $this->assertInstanceOf(EnvManager::class, $this->manager);
        $this->assertSame('Example', $this->manager->get('APP_NAME'));

        $this->assertTrue($this->manager->save());
        $this->assertTrue($this->manager->fileExists());

        // По окончании проверки Удалим временный файл.
        unlink($this->manager->filePath());
    }

    /**
     * @covers ::newFromPath
     */
    public function test_new_from_path(): void
    {
        $this->assertFalse($this->manager->fileExists());

        $filePath = $this->environmentFilePath.'.example';

        // Перед проверкой переменных файла создадим его.
        file_put_contents($filePath, $this->sampleTestingStringableContent(), LOCK_EX);

        $this->manager = new LaravelCollectionEnvManager($this->environmentFilePath, $this->cipher);
        $this->assertInstanceOf(EnvManager::class, $this->manager);
        $this->assertNull($this->manager->get('APP_NAME'));

        $this->manager->newFromPath($filePath);

        $this->assertSame($filePath, $this->manager->filePath());
        $this->assertSame('Example', $this->manager->get('APP_NAME'));

        // По окончании проверки Удалим временный файл.
        unlink($this->manager->filePath());
    }

    /**
     * @covers ::withNewAppKey
     */
    public function test_new_from_path_with_new_app_key(): void
    {
        $this->assertFalse($this->manager->fileExists());

        $filePath = $this->environmentFilePath.'.example';

        // Перед проверкой переменных файла создадим его.
        file_put_contents($filePath, $this->sampleTestingStringableContent(), LOCK_EX);

        $this->manager = new LaravelCollectionEnvManager($this->environmentFilePath, $this->cipher);
        $this->assertInstanceOf(EnvManager::class, $this->manager);
        $this->assertNull($this->manager->get('APP_NAME'));
        $this->assertNull($this->manager->get('APP_KEY'));

        $this->manager->newFromPath($filePath)
            ->withNewAppKey();

        $this->assertSame('Example', $this->manager->get('APP_NAME'));
        $this->assertNotNull($this->manager->get('APP_KEY'));

        // По окончании проверки Удалим временный файл.
        unlink($this->manager->filePath());
    }

    /**
     * [sampleTestingContent description].
     *
     * @return array
     */
    protected function sampleTestingArrayContent(): array
    {
        return [
            'APP_NAME' => 'Example',
            'APP_LOCALE' => 'ru',
            'APP_URL' => 'https://example.com',
            'MAIL_FROM_ADDRESS' => 'from@example.com',
            'MAIL_FROM_NAME' => 'Hercules',
        ];
    }

    /**
     * [sampleTestingContent description].
     *
     * @return string
     */
    protected function sampleTestingStringableContent(): string
    {
        $data = $this->sampleTestingArrayContent();

        return implode(PHP_EOL, array_map(
            function (string $key, string $value): string {
                return "{$key}={$value}";
            },
            array_keys($data),
            $data
        ));
    }
}
