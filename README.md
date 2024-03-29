# Менеджер файла переменных окружения Laravel 9.x.

Данный менеджер используется только для физического взаимодействия с файлом переменных окружения.

> **NB**
>
> Текущий файл окружения, подгруженный для чтения / редактирования и корневой файл окружения `.env` могут быть разными, но сохранение всегда производится в корневой файл, указанный при инициализации экземпляра менеджера.
> При регистрации в рамках поставщика службы использован шаблон Одиночка (*Singleton*).

## Подключение

Для добавления зависимости в проект на Laravel, используйте менеджер пакетов Composer:

```console
composer require russsiq/laravel-env-manager
```

Если в вашем приложении включен отказ от обнаружения пакетов в директиве `dont-discover` в разделе `extra` файла `composer.json`, то необходимо самостоятельно добавить следующее в файле `config/app.php`:

- Провайдер услуг в раздел `providers`:

```php
Russsiq\EnvManager\EnvManagerServiceProvider::class,
```

- Псевдоним класса (Facade) в раздел `aliases`:

```php
'EnvManager' => Russsiq\EnvManager\Support\Facades\EnvManager::class,
```

## Использование

### Методы

Все публичные методы доступны через фасад `EnvManager`:

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
 - [withNewAppKey](#method-withNewAppKey)

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
##### `newFromPath(string $filePath): self`
Создать файл окружения путем копирования содержимого файла по указанному полному пути. Полная перезагрузка переменных окружения.

<a name="method-withNewAppKey"></a>
##### `withNewAppKey(): self`
Создать новый ключ приложения.

### Пример использования

```php
use Russsiq\EnvManager\Facades\EnvManager;

// Если файл не существует.
if (! EnvManager::fileExists()) {

    // Создаем новый файл из образца.
    EnvManager::newFromPath(base_path('.env.example'))
        // Попутно генерируем ключ для приложения.
        ->withNewAppKey()
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

## Тестирование

Для запуска тестов используйте команду:

```console
composer run-script test
```

Для запуска тестов и формирования agile-документации, генерируемой в HTML-формате и записываемой в файл [tests/testdox.html](tests/testdox.html), используйте команду:

```console
composer run-script testdox
```

## Удаление пакета

Для удаления пакета из вашего проекта на Laravel используйте команду:

```console
composer remove russsiq/laravel-env-manager
```

## Лицензия

`laravel-env-manager` – программное обеспечение с открытым исходным кодом, распространяющееся по лицензии [MIT](LICENSE).
