<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Factories;

use Alexeykhr\ClickhouseMigrations\Stubs\TableMigrationStub;
use Alexeykhr\ClickhouseMigrations\Stubs\DefaultMigrationStub;
use Alexeykhr\ClickhouseMigrations\Contracts\MigrationStubContract;
use Alexeykhr\ClickhouseMigrations\Exceptions\ClickhouseStubException;

class FactoryStub
{
    /**
     * @param  string  $type
     * @param  string|null  $path
     * @return MigrationStubContract
     * @throws ClickhouseStubException
     */
    public static function create(string $type = 'default', ?string $path = null): MigrationStubContract
    {
        if ($stub = self::getStubs()[$type] ?? null) {
            return app($stub, [
                'path' => $path ?? config('clickhouse.migrations.stubPath'),
            ]);
        }

        throw new ClickhouseStubException("ClickHouse stub {$type} does not exist");
    }

    /**
     * @param  string  $type
     * @param  string|null  $path
     * @return MigrationStubContract
     * @throws ClickhouseStubException
     */
    public static function createPackage(string $type = 'default', ?string $path = null): MigrationStubContract
    {
        if ($stub = self::getPackageStubs()[$type] ?? null) {
            return app($stub, [
                'path' => $path ?? config('clickhouse.migrations.stubPath'),
            ]);
        }

        throw new ClickhouseStubException("ClickHouse stub {$type} does not exist");
    }

    /**
     * @return array
     */
    public static function getStubs(): array
    {
        return array_merge([
            'table' => TableMigrationStub::class,
            'default' => DefaultMigrationStub::class,
        ], self::getPackageStubs());
    }

    /**
     * @return array
     */
    public static function getPackageStubs(): array
    {
        return config('clickhouse.stubs', []);
    }
}
