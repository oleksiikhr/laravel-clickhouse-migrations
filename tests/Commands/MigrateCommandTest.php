<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests\Commands;

use Alexeykhr\ClickhouseMigrations\Tests\TestCase;

class MigrateCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testMigrateWithEmptyFiles(): void
    {
        $this->artisan('clickhouse-migrate');

        $this->assertClickhouseTotal(0);
    }

    /**
     * @return void
     */
    public function testMigrateWithSingleFile(): void
    {
        $this->useMigrations(['2020_01_01_000000_create_users_table']);

        $this->artisan('clickhouse-migrate');

        $this->assertClickhouseTotal(1);
        $this->assertClickhouseContainsMigration('2020_01_01_000000_create_users_table');
    }
}
