<?php

declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests\Commands;

use Illuminate\Filesystem\Filesystem;
use Alexeykhr\ClickhouseMigrations\Tests\TestCase;
use Alexeykhr\ClickhouseMigrations\Stubs\MigrationStub;
use Alexeykhr\ClickhouseMigrations\Tests\assets\MyCustomStubHandler;
use Alexeykhr\ClickhouseMigrations\Contracts\MigrationCreatorContract;
use Alexeykhr\ClickhouseMigrations\Tests\assets\MigrationCreatorExtended;

class MigrateMakeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(MigrationCreatorContract::class, static function ($app) {
            return new MigrationCreatorExtended(
                app(Filesystem::class),
                app(MigrationStub::class)
            );
        });
    }

    /**
     * @return void
     */
    public function testSimpleMigration(): void
    {
        $this->artisan('make:clickhouse-migration create_users_table');

        $this->assertClickhouseMigrationFile('users');
    }

    /**
     * @return void
     */
    public function testCustomStubWithCustomParamsAndHandler(): void
    {
        config(['clickhouse.stubs' => ['customStub' => $this->assetsPath('stubs/dynamic.stub')]]);

        $this->artisan('make:clickhouse-migration', [
            'name' => 'create_users_table',
            '--stub' => 'customStub',
            '--stub.param' => 'myCustomParameter:my_custom_table',
            '--stub.handler' => MyCustomStubHandler::class,
        ]);

        $this->assertClickhouseMigrationFile('users');
        $this->artisan('clickhouse-migrate');
        $this->assertClickhouseContainsMigration('users');
        $this->assertClickhouseTableExists('my_custom_table');
    }

    /**
     * @return void
     */
    public function testTableOption(): void
    {
        config(['clickhouse.stubs' => ['table' => $this->assetsPath('stubs/table.stub')]]);

        $this->artisan('make:clickhouse-migration', [
            'name' => 'create_products_table',
            '--table' => 'another_table',
        ]);

        $this->assertClickhouseMigrationFile('products');
        $this->artisan('clickhouse-migrate');
        $this->assertClickhouseContainsMigration('products');
        $this->assertClickhouseTableExists('another_table');
    }
}
