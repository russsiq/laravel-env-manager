<?php

declare(strict_types=1);

namespace Tests\Feature\Support;

use PHPUnit\Framework\TestCase;
use Russsiq\EnvManager\Contracts\EnvManager as EnvManagerContract;
use Russsiq\EnvManager\Support\LaravelCollectionEnvManager;

/**
 * @coversDefaultClass \Russsiq\EnvManager\Support\LaravelCollectionEnvManager
 */
class LaravelCollectionEnvManagerTest extends TestCase
{
    private const DUMMY_DIR = __DIR__.'/tmp';

    private const DUMMY_CIPHER = 'AES-256-CBC';

    /**
     * Полный путь к файлу окружения приложения.
     *
     * @var string
     */
    private $environmentFilePath;

    /**
     * Алгоритм, используемый для шифрования.
     *
     * @var string
     */
    private $cipher;

    /**
     * Экземпляр менеджера.
     *
     * @var EnvManager
     */
    private $manager;

    /**
     * Этот метод вызывается перед запуском
     * первого теста этого класса тестирования.
     *
     * @return void
     */
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

    /**
     * Этот метод вызывается перед каждым тестом.
     *
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
     *
     * @return void
     */
    protected function tearDown(): void
    {
        // Очищаем кеш состояния файлов,
        // так как тестирование выполняется
        // на реальной файловой системе.
        clearstatcache();
    }

    /**
     * Этот метод вызывается после запуска
     * последнего теста этого класса тестирования.
     *
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
     * @covers ::__construct
     *
     * Экземпляр менеджера успешно создан.
     *
     * @return void
     */
    public function testSuccessfullyInitiated(): void
    {
        $this->assertInstanceOf(EnvManagerContract::class, $this->manager);
    }

    /**
     * @test
     * @covers ::filePath
     *
     * Подтвердить актуальность файла окружения.
     *
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
     * @covers ::setFilePath
     *
     * Подтвердить успешность задания полного пути
     * текущего файла окружения.
     *
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
     * @covers ::resetFilePath
     * @depends testSetFilePath
     *
     * Подтвердить успешность сброса полного пути
     * текущего файла окружения.
     *
     * @return void
     */
    public function testResetFilePath(): void
    {
        $this->manager->resetFilePath();

        $this->assertEquals($this->environmentFilePath, $this->manager->filePath());
    }

    /**
     * @test
     * @covers ::fileExists
     *
     * Подтвердить физическое присутствие файла окружения.
     *
     * @return void
     */
    public function testFileExists(): void
    {
        $filePath = $this->manager->filePath();

        // Перед проверкой существования файла создадим его.
        file_put_contents($filePath, $this->simpleTestingStringableContent(), LOCK_EX);

        $this->assertFileExists($filePath);
        $this->assertTrue($this->manager->fileExists());
        $this->assertEquals($filePath, $this->environmentFilePath);

        // Удалим временный файл, что необходимо для зависимого теста.
        unlink($this->manager->filePath());
    }

    /**
     * @test
     * @covers ::fileExists
     * @depends testFileExists
     *
     * Подтвердить физическое отсутствие файла окружения.
     *
     * @return void
     */
    public function testFileNotExists(): void
    {
        $this->assertFalse($this->manager->fileExists());
    }

    /**
     * @test
     * @covers ::has
     *
     * [testHasIsTrue description]
     *
     * @return void
     */
    public function testHasIsTrue(): void
    {
        // Перед проверкой переменных файла создадим его.
        file_put_contents($this->environmentFilePath, $this->simpleTestingStringableContent(), LOCK_EX);

        $this->manager = new EnvManager($this->environmentFilePath, $this->cipher);
        $this->assertInstanceOf(EnvManagerContract::class, $this->manager);
        $this->assertTrue($this->manager->has('APP_NAME'));

        // По окончании проверки Удалим временный файл.
        unlink($this->manager->filePath());
    }

    /**
     * @test
     * @covers ::has
     *
     * [testHasIsFalse description]
     *
     * @return void
     */
    public function testHasIsFalse(): void
    {
        $this->assertFalse($this->manager->has('not-exist'));
    }

    /**
     * @test
     * @covers ::get
     *
     * [testGet description]
     *
     * @return void
     */
    public function testGet(): void
    {
        // Перед проверкой переменных файла создадим его.
        file_put_contents($this->environmentFilePath, $this->simpleTestingStringableContent(), LOCK_EX);

        $this->manager = new EnvManager($this->environmentFilePath, $this->cipher);
        $this->assertInstanceOf(EnvManagerContract::class, $this->manager);
        $this->assertSame('Example', $this->manager->get('APP_NAME'));

        // По окончании проверки Удалим временный файл.
        unlink($this->manager->filePath());
    }

    /**
     * @test
     * @covers ::get
     *
     * [testGetWithDefault description]
     *
     * @return void
     */
    public function testGetWithDefault(): void
    {
        // Перед проверкой переменных файла создадим его.
        file_put_contents($this->environmentFilePath, $this->simpleTestingStringableContent(), LOCK_EX);

        $this->manager = new EnvManager($this->environmentFilePath, $this->cipher);
        $this->assertInstanceOf(EnvManagerContract::class, $this->manager);
        $this->assertSame('default', $this->manager->get('not-exist', 'default'));

        // По окончании проверки Удалим временный файл.
        unlink($this->manager->filePath());
    }

    /**
     * @test
     * @covers ::set
     *
     * [testSet description]
     *
     * @return void
     */
    public function testSet(): void
    {
        $this->manager->set('key', 'value');

        $this->assertSame('value', $this->manager->get('key'));
    }

    /**
     * @test
     * @covers ::setMany
     *
     * [testSet description]
     *
     * @return void
     */
    public function testSetMany(): void
    {
        $this->manager->setMany([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);

        $this->assertSame('value1', $this->manager->get('key1'));
        $this->assertSame('value2', $this->manager->get('key2'));
    }

    /**
     * @test
     * @covers ::save
     *
     * [testSave description]
     *
     * @return void
     */
    public function testSave(): void
    {
        $this->assertFalse($this->manager->fileExists());

        // Перед проверкой переменных файла создадим его.
        file_put_contents($this->environmentFilePath, $this->simpleTestingStringableContent(), LOCK_EX);

        $this->manager = new EnvManager($this->environmentFilePath, $this->cipher);
        $this->assertInstanceOf(EnvManagerContract::class, $this->manager);
        $this->assertSame('Example', $this->manager->get('APP_NAME'));

        $this->assertTrue($this->manager->save());
        $this->assertTrue($this->manager->fileExists());

        // По окончании проверки Удалим временный файл.
        unlink($this->manager->filePath());
    }

    /**
     * @test
     * @covers ::newFromPath
     *
     * [testNewFromPath description]
     *
     * @return void
     */
    public function testNewFromPath(): void
    {
        $this->assertFalse($this->manager->fileExists());

        $filePath = $this->environmentFilePath.'.example';

        // Перед проверкой переменных файла создадим его.
        file_put_contents($filePath, $this->simpleTestingStringableContent(), LOCK_EX);

        $this->manager = new EnvManager($this->environmentFilePath, $this->cipher);
        $this->assertInstanceOf(EnvManagerContract::class, $this->manager);
        $this->assertNull($this->manager->get('APP_NAME'));

        $this->manager->newFromPath($filePath);

        $this->assertSame($filePath, $this->manager->filePath());
        $this->assertSame('Example', $this->manager->get('APP_NAME'));

        // По окончании проверки Удалим временный файл.
        unlink($this->manager->filePath());
    }

    /**
     * @test
     * @covers ::withNewAppKey
     *
     * [testNewFromPathWithAppKey description]
     *
     * @return void
     */
    public function testNewFromPathWithAppKey(): void
    {
        $this->assertFalse($this->manager->fileExists());

        $filePath = $this->environmentFilePath.'.example';

        // Перед проверкой переменных файла создадим его.
        file_put_contents($filePath, $this->simpleTestingStringableContent(), LOCK_EX);

        $this->manager = new EnvManager($this->environmentFilePath, $this->cipher);
        $this->assertInstanceOf(EnvManagerContract::class, $this->manager);
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
     * [simpleTestingContent description].
     *
     * @return array
     */
    protected function simpleTestingArrayContent(): array
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
     * [simpleTestingContent description].
     *
     * @return string
     */
    protected function simpleTestingStringableContent(): string
    {
        $data = $this->simpleTestingArrayContent();

        return implode(PHP_EOL, array_map(
            function (string $key, string $value): string {
                return "{$key}={$value}";
            },
            array_keys($data),
            $data
        ));
    }
}
