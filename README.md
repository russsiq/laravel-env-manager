## Менеджер файла переменных окружения Laravel 6.x.

Менеджер файла переменных окружения `.env` для Laravel 6.x.

### Подключение

 - **1** Для добавления зависимости в проект на Laravel в файле `composer.json`

    ```json
    "require": {
        "russsiq/laravel-env-manager": "dev-master"
    }
    ```

 - **2** Для подключения в уже созданный проект воспользуйтесь командной строкой:

    ```console
    composer require russsiq/laravel-env-manager:dev-master
    ```

 - **3** В файле `config/app.php` добавьте:

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
 - [fileExists](#method-fileExists)
 - [has](#method-has)
 - [get](#method-get)
 - [set](#method-set)
 - [setMany](#method-setMany)
 - [save](#method-save)
 - [newFromPath](#method-newFromPath)

<a name="method-filePath"></a>
##### `filePath(): string`
Получить полный путь к файлу окружения.

<a name="method-fileExists"></a>
##### `fileExists(): bool`
Проверить физическое существование файла окружения.

<a name="method-has"></a>
##### `has(string $name): bool`
Проверить существование значения для указанной переменной окружения.

<a name="method-get"></a>
##### `get(string $name, $default = null)`
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

### Удаление пакета из вашего проекта на Laravel
```console
composer remove russsiq/laravel-env-manager
```

### Тестирование

Неа, не слышал.

### Лицензия

laravel-env-manager - программное обеспечение с открытым исходным кодом, распространяющееся по лицензии [MIT](https://choosealicense.com/licenses/mit/).
