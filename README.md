# Laravel Clickhouse Migrations

![Build](https://github.com/Alexeykhr/laravel-clickhouse-migrations/workflows/PHP%20Composer/badge.svg)
[![Latest Stable Version](https://img.shields.io/packagist/v/alexeykhr/laravel-clickhouse-migrations.svg)](https://packagist.org/packages/alexeykhr/laravel-clickhouse-migrations)
[![codecov](https://codecov.io/gh/Alexeykhr/laravel-clickhouse-migrations/branch/master/graph/badge.svg)](https://codecov.io/gh/Alexeykhr/laravel-clickhouse-migrations)
[![Total Downloads](https://img.shields.io/packagist/dt/alexeykhr/laravel-clickhouse-migrations.svg)](https://packagist.org/packages/alexeykhr/laravel-clickhouse-migrations)
[![Downloads Month](https://img.shields.io/packagist/dm/alexeykhr/laravel-clickhouse-migrations.svg)](https://packagist.org/packages/alexeykhr/laravel-clickhouse-migrations)

---

- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Advances Usage](#advanced-usage)
    - [New Migration](#new-migration)
    - [Up Migrations](#up-migrations)
    - [Down Migrations](#down-migrations)
    - [Other](#other)
- [Changelog](#changelog)
- [License](#license)

## Installation

Install this package via [Composer](https://getcomposer.org/).

```shell script
$ composer require alexeykhr/laravel-clickhouse-migrations
```

> Note: **PHP 7.2.5 or later** is required

Publish Clickhouse configs:

```shell script
$ php artisan vendor:publish --provider='Alexeykhr\ClickhouseMigrations\Providers\MigrationProvider'
```

## Basic Usage

Create a new migration file:

```shell script
$ php artisan make:clickhouse-migration {name}
```

Up migrations:

```shell script
$ php artisan clickhouse-migrate
```

Down last migration:

```shell script
$ php artisan clickhouse-migration:rollback
```

## Advanced Usage

### New Migration

To create a new migration, use this command:

```shell script
$ php artisan make:clickhouse-migration {name}
```

For example:

```shell script
$ php artisan make:clickhouse-migration create_users_table
```

The new file will be located at the path specified in the configs: **clickhouse.path**

#### Table

You can use a more prepared stub from the library that contains a template for
quickly adding a new table by adding the **--table** option:

```shell script
$ php artisan make:clickhouse-migration create_users_table --table=users
```

#### Path

You can override the path to the migrations folder by passing the **--path** option:

```shell script
$ php artisan make:clickhouse-migration create_users_table --path=database/new-migrations-folder
```

If you want to use an absolute path to the file, add one more option - **--realpath** to the existing option:

```shell script
$ php artisan make:clickhouse-migration create_users_table --path=/path/to/migrations --realpath
```

#### Stub

You can use your (override) prepared stub when creating a new file, list: **clickhouse.stubs**:

For example:

```shell script
$ php artisan make:clickhouse-migration create_users_table --stub=default
```

Also you can add **Handlers**, with them, you can customize over one stub every time you create a file:

```shell script
$ php artisan make:clickhouse-migration create_users_table --stub=myStub --stub.handler='App\Clickhouse\MyHandler'
```

> The class must implement the `Alexeykhr\ClickhouseMigrations\Contracts\MigrationStubHandlerContract` interface

You can pass your `$parameters`, for example:

```shell script
$ php artisan make:clickhouse-migration create_users_table --stub.handler='App\Clickhouse\MyHandler' --stub.param=key:value --stub.param=table:products
```

You can also register a global handler that will apply to all generated stub files: `clickhouse.handlers.global`

### Up Migrations

[Path option with Realpath](#path)

#### Force

To remove the interactive question during production migrations, you can use **--force** option:

```shell script
$ php artisan clickhouse-migrate --force
```

#### Output

To output migrations to be applied use **--output** option:

```shell script
$ php artisan clickhouse-migrate --output
```

Before applying the shown migrations - will display an interactive question, to remove it,
you can add another **--force** option to this option:

```shell script
$ php artisan clickhouse-migrate --output --force
```

#### Step

You can specify how many files need to be applied:

```shell script
$ php artisan clickhouse-migrate --step=1
```

> Value `0` - all files

### Down Migrations

Has the same properties as in [Up Migrations](#up-migrations).

### Other

You can use a singleton object [smi2/phpClickHouse](https://github.com/smi2/phpClickHouse#start) to query ClickHouse (used in migrations):

```php
app('clickhouse')->select(/* Query */);
app('clickhouse')->write(/* Query */);
```

## Changelog

Detailed changes for each release are documented in the [CHANGELOG.md](https://github.com/Alexeykhr/laravel-clickhouse-migrations/blob/master/CHANGELOG.md).

## License

[MIT](https://opensource.org/licenses/MIT)
