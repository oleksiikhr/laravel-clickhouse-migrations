<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests\Commands;

use Alexeykhr\ClickhouseMigrations\Tests\TestCase;

class MigrateCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testEmptyFiles(): void
    {
        $this->artisan('clickhouse-migrate');

        $this->assertClickhouseTotal(0);
    }

    /**
     * @return void
     */
    public function testSingleFile(): void
    {
        $this->useMigrations(['2020_01_01_000000_create_users_table']);

        $this->artisan('clickhouse-migrate');

        $this->assertClickhouseTotal(1);
        $this->assertClickhouseContainsMigration('2020_01_01_000000_create_users_table');
    }

    /**
     * @return void
     */
    public function testOutputWithNotApply(): void
    {
        $this->useMigrations(['2020_01_01_000000_create_users_table']);

        $this->artisan('clickhouse-migrate', [
            '--output' => true,
        ])->expectsConfirmation('Apply migrations?', 'no');

        $this->assertClickhouseTotal(0);
    }

    /**
     * @return void
     */
    public function testOutputWithApply(): void
    {
        $this->useMigrations(['2020_01_01_000000_create_users_table']);

        $this->artisan('clickhouse-migrate', [
            '--output' => true,
        ])->expectsConfirmation('Apply migrations?', 'yes');

        $this->assertClickhouseTotal(1);
    }

    /**
     * @return void
     */
    public function testOutputWithForce(): void
    {
        $this->useMigrations(['2020_01_01_000000_create_users_table']);

        $this->artisan('clickhouse-migrate', [
            '--output' => true,
            '--force' => true,
        ]);

        $this->assertClickhouseTotal(1);
    }

    /**
     * @return void
     */
    public function testCustomRealPath(): void
    {
        $this->useMigrations(['2020_01_01_000000_create_users_table']);

        $this->artisan('clickhouse-migrate', [
            '--path' => $this->dynamicPath('migrations'),
            '--realpath' => true,
        ]);

        $this->assertClickhouseTotal(1);
    }

    /**
     * @return void
     */
    public function testZeroStep(): void
    {
        $this->useMigrations(['2020_01_01_000000_create_users_table', '2020_01_01_000000_create_users2_table']);

        $this->artisan('clickhouse-migrate', [
            '--step' => 0,
        ]);

        $this->assertClickhouseTotal(2);
        $this->assertClickhouseContainsMigration('2020_01_01_000000_create_users2_table');
    }

    /**
     * @return void
     */
    public function testSingleStep(): void
    {
        $this->useMigrations(['2020_01_01_000000_create_users_table', '2020_01_01_000000_create_users2_table']);

        $this->artisan('clickhouse-migrate', [
            '--step' => 1,
        ]);

        $this->assertClickhouseTotal(1);
    }

    /**
     * @return void
     */
    public function testTwoStep(): void
    {
        $this->useMigrations(['2020_01_01_000000_create_users_table', '2020_01_01_000000_create_users2_table']);

        $this->artisan('clickhouse-migrate', [
            '--step' => 2,
        ]);

        $this->assertClickhouseTotal(2);
    }

    /**
     * @return void
     */
    public function testTenStep(): void
    {
        $this->useMigrations(['2020_01_01_000000_create_users_table', '2020_01_01_000000_create_users2_table']);

        $this->artisan('clickhouse-migrate', [
            '--step' => 10,
        ]);

        $this->assertClickhouseTotal(2);
    }
}
