# Laravel Clickhouse Migrations

![Build](https://github.com/Alexeykhr/laravel-clickhouse-migrations/workflows/PHP%20Composer/badge.svg)
[![Latest Stable Version](https://img.shields.io/packagist/v/alexeykhr/laravel-clickhouse-migrations.svg)](https://packagist.org/packages/alexeykhr/laravel-clickhouse-migrations)
[![Total Downloads](https://img.shields.io/packagist/dt/alexeykhr/laravel-clickhouse-migrations.svg)](https://packagist.org/packages/alexeykhr/laravel-clickhouse-migrations)
[![Downloads Month](https://img.shields.io/packagist/dm/alexeykhr/laravel-clickhouse-migrations.svg)](https://packagist.org/packages/alexeykhr/laravel-clickhouse-migrations)

---

- [Installation](#installation)
- [Basic Usage](#basic-usage)
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

## Changelog

Detailed changes for each release are documented in the [CHANGELOG.md](https://github.com/Alexeykhr/laravel-clickhouse-migrations/blob/master/CHANGELOG.md).

## License

[MIT](https://opensource.org/licenses/MIT)
