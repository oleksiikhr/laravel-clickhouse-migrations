<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations;

use Alexeykhr\ClickhouseMigrations\Stubs\TableMigrationStub;
use Alexeykhr\ClickhouseMigrations\Stubs\DefaultMigrationStub;
use Alexeykhr\ClickhouseMigrations\Contracts\ClickhouseStubContract;
use Alexeykhr\ClickhouseMigrations\Exceptions\ClickhouseStubException;

class StubFactory
{
    /**
     * @param  string  $type
     * @return ClickhouseStubContract
     * @throws ClickhouseStubException
     */
    public static function create(string $type = 'default'): ClickhouseStubContract
    {
        if ($stub = self::getExistsStubs()[$type] ?? null) {
            return app($stub);
        }

        throw new ClickhouseStubException("ClickHouse stub {$type} does not exist");
    }

    /**
     * @return array
     */
    public static function getExistsStubs(): array
    {
        return [
            'table' => TableMigrationStub::class,
            'default' => DefaultMigrationStub::class,
        ];
    }
}
