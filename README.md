## Менеджер файла переменных окружения Laravel 7.x.

Используется только для физического взаимодействия с файлом.

> **NB** Текущий файл окружения, подгруженный для чтения/редактирования и корневой файл окружения `.env` могут быть разными, но сохранение всегда производится в корневой файл.

Содержание:

1. [Подключение](#Подключение)
1. [Использование](#Использование)
1. [Тестирование](#Тестирование)
1. [Удаление пакета](#Удаление-пакета)
1. [Лицензия](#Лицензия)

### Подключение

 - **1** Для добавления зависимости в проект на Laravel в файле `composer.json`

    ```json
    "require": {
        "russsiq/laravel-env-manager": "^0.2"
    }
    ```

 - **2** Для подключения в уже созданный проект воспользуйтесь командной строкой:

    ```console
    composer require "russsiq/laravel-env-manager:^0.2"
    ```

 - **3** Если в вашем приложении включен отказ от обнаружения пакетов в директиве `dont-discover` в разделе `extra` файла `composer.json`, то необходимо самостоятельно добавить в файле `config/app.php`:

    - **3.1** Провайдер услуг в раздел `providers`:

        ```php
        Russsiq\EnvManager\EnvManagerServiceProvider::class,
        ```

    - **3.2** Псевдоним класса (Facade) в раздел `aliases`:

        ```php
        'EnvManager' => Russsiq\EnvManager\Support\Facades\EnvManager::class,
        ```

### Использование

#### Методы

Все публичные методы менеджера доступны через фасад `EnvManager`:

```php
EnvManager::someMethod(example $someParam);
```

Список доступных публичных методов:

 - [filePath](#method-filePath)
 - [setFilePath](#method-setFilePath)
 - [resetFilePath](#method-resetFilePath)
 - [fileExists](#method-fileExists)
 - [has](#method-has)
 - [get](#method-get)
 - [set](#method-set)
 - [setMany](#method-setMany)
 - [save](#method-save)
 - [newFromPath](#method-newFromPath)

<a name="method-filePath"></a>
##### `filePath(): string`
Получить полный путь к текущему файлу окружения.

<a name="method-setFilePath"></a>
##### `setFilePath(string $filePath): self`
Установить полный путь к текущему файлу окружения.

<a name="method-resetFilePath"></a>
##### `resetFilePath(): self`
Сбросить полный путь к текущему файлу окружения.

<a name="method-fileExists"></a>
##### `fileExists(): bool`
Проверить физическое существование текущего файла окружения.

<a name="method-has"></a>
##### `has(string $name): bool`
Проверить существование значения для указанной переменной окружения.

<a name="method-get"></a>
##### `get(string $name, $default = null): ?string`
Получить значение для указанной переменной окружения.

<a name="method-set"></a>
##### `set(string $name, $value): self`
Установить значение для переменной окружения.

<a name="method-setMany"></a>
##### `setMany(array $data): self`
Установить значения для переменных окружения.

<a name="method-save"></a>
##### `save(): bool`
Сохранить файл окружения.

<a name="method-newFromPath"></a>
##### `newFromPath(string $filePath, bool $withAppKey = false): self`
Создать файл окружения путем копирования содержимого файла по указанному полному пути. Полная перезагрузка переменных окружения. Если параметр `$withAppKey` указан как `true`, то будет сгенерирован новый ключ приложения `APP_KEY`.

#### Пример использования

```php
use Russsiq\EnvManager\Facades\EnvManager;

// Если файл не существует.
if (! EnvManager::fileExists()) {

    // Создаем новый файл из образца,
    // попутно генерируя ключ для приложения.
    EnvManager::newFromPath(base_path('.env.example'), true)
        // Устанавливаем необходимые значения.
        ->setMany([
            'APP_NAME' => 'Example site',
            'APP_LOCALE' => 'ru',
            'APP_URL' => url('/'),
            'MAIL_FROM_ADDRESS' => 'from@example.com',
            'MAIL_FROM_NAME' => 'Example',
        ])
        // Сохраняем новый файл в корне как `.env`.
        ->save();

}

// Распечатаем для примера
dump(EnvManager::get('APP_NAME')); // -> `Example site`
```

### Тестирование

Для запуска тестов используйте команду:

```console
composer run-script test
```

Для запуска тестов под Windows 7 используйте команду:

```console
composer run-script test-win7
```

Для формирования agile-документации, генерируемой в HTML-формате и записываемой в файл [tests/testdox.html](tests/testdox.html), используйте команду:

```console
composer run-script testdox
```

### Удаление пакета

Для удаления пакета из вашего проекта на Laravel используйте команду:

```console
composer remove russsiq/laravel-env-manager
```

### Лицензия

`laravel-env-manager` – программное обеспечение с открытым исходным кодом, распространяющееся по лицензии [MIT](https://choosealicense.com/licenses/mit/).
