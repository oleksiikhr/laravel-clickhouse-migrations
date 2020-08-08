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

        self::assertEquals(0, $this->repository()->total());
    }

    /**
     * @return void
     */
    public function testMigrateWithSingleFile(): void
    {
        $this->useMigrations(['2020_01_01_000000_create_users_table']);

        $this->artisan('clickhouse-migrate');

        self::assertEquals(1, $this->repository()->total());

        $migration = $this->repository()->find('2020_01_01_000000_create_users_table');

        self::assertContains('2020_01_01_000000_create_users_table', $migration);
    }
}
