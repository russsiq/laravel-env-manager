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

Полный синтаксис метода с типом возвращаемых данных | Список допустимых параметров | Описание
--------------------------------------------------- | ---------------------------- | --------
`filePath(): string` |  | Получить полный путь к файлу окружения.
`fileExists(): bool` |  | Проверить физическое существование файла окружения.
`has(string $name): bool` | `$name`: имя переменной | Проверить существование значения для указанной переменной окружения.
`get(string $name, $default = null)` | `$name`: имя переменной<br>`$default`: значение по умолчанию | Проверить существование значения для указанной переменной окружения.
`set(string $name, $value): self` | `$name`: имя переменной<br>`$value`: значение по умолчанию | Установить значение для переменной окружения.
`setMany(array $data): self` | `$data`: массив переменных | Установить значения для переменных окружения.

### Удаление пакета из вашего проекта на Laravel
```console
composer remove russsiq/laravel-env-manager
```

### Тестирование

Неа, не слышал.

### Лицензия

laravel-env-manager - программное обеспечение с открытым исходным кодом, распространяющееся по лицензии [MIT](https://choosealicense.com/licenses/mit/).
