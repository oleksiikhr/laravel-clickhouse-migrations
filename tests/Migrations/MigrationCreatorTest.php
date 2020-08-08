<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests\Migrations;

use Illuminate\Filesystem\Filesystem;
use Alexeykhr\ClickhouseMigrations\Tests\TestCase;
use Alexeykhr\ClickhouseMigrations\Stubs\MigrationStub;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Alexeykhr\ClickhouseMigrations\Migrations\MigrationCreator;
use Alexeykhr\ClickhouseMigrations\Tests\assets\Handlers\MyCustomStubHandler;

class MigrationCreatorTest extends TestCase
{
    /**
     * @return void
     * @throws FileNotFoundException
     */
    public function testMakeCompleteStubAndMigrateAndRollbackMigration(): void
    {
        $filesystem = new Filesystem();
        $stub = new MigrationStub($filesystem);
        $creator = new MigrationCreator($filesystem, $stub);

        $creator->create(
            $this->assetsPath('stubs/complete.stub'),
            'MyMigrationPath',
            $this->dynamicPath('migrations')
        );

        $this->artisan('clickhouse-migrate');
        $this->assertClickhouseTotal(1);
        $this->artisan('clickhouse-migrate:rollback');
        $this->assertClickhouseTotal(0);
    }

    /**
     * @return void
     * @throws FileNotFoundException
     */
    public function testMakeDynamicStubAndUseCustomHandlers(): void
    {
        $filesystem = new Filesystem();
        $stub = new MigrationStub($filesystem, [app(MyCustomStubHandler::class)]);
        $creator = new MigrationCreator($filesystem, $stub);

        $creator->create(
            $this->assetsPath('stubs/dynamic.stub'),
            'MyMigrationPath',
            $this->dynamicPath('migrations'),
            ['myCustomParameter' => 'products']
        );

        $this->artisan('clickhouse-migrate');
        $this->assertClickhouseTotal(1);
        $this->artisan('clickhouse-migrate:rollback');
        $this->assertClickhouseTotal(0);
    }
}
