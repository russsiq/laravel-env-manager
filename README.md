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

### Удаление пакета из вашего проекта на Laravel
```console
composer remove russsiq/laravel-env-manager
```

### Тестирование

Неа, не слышал.

### Лицензия

laravel-env-manager - программное обеспечение с открытым исходным кодом, распространяющееся по лицензии [MIT](https://choosealicense.com/licenses/mit/).
