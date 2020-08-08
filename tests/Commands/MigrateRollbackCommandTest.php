<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests\Commands;

use Alexeykhr\ClickhouseMigrations\Tests\TestCase;

class MigrateRollbackCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testEmptyMigrations(): void
    {
        $this->artisan('clickhouse-migrate:rollback');

        $this->assertClickhouseTotal(0);
    }

    /**
     * @return void
     */
    public function testSingleMigration(): void
    {
        $this->useMigrations(['users1']);
        $this->artisan('clickhouse-migrate');

        $this->artisan('clickhouse-migrate:rollback');

        $this->assertClickhouseTotal(0);
    }

    /**
     * @return void
     */
    public function testTwoMigrations(): void
    {
        $this->useMigrations(['users1', 'users2']);
        $this->artisan('clickhouse-migrate');

        $this->artisan('clickhouse-migrate:rollback');

        $this->assertClickhouseTotal(1);
    }

    /**
     * @return void
     */
    public function testTwoMigrationsWithZeroStep(): void
    {
        $this->useMigrations(['users1', 'users2']);
        $this->artisan('clickhouse-migrate');

        $this->artisan('clickhouse-migrate:rollback', [
            '--step' => 0,
        ]);

        $this->assertClickhouseTotal(0);
    }

    /**
     * @return void
     */
    public function testThreeMigrationsWithExceptionAndZeroStep(): void
    {
        $this->useMigrations(['users1', 'users2', 'users3_exception']);

        try {
            $this->artisan('clickhouse-migrate');
        } catch (\Exception $e) {
            // nothing
        }

        $this->artisan('clickhouse-migrate:rollback', [
            '--step' => 0,
        ]);

        $this->assertClickhouseTotal(0);
    }
}
