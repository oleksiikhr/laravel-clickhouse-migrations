<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests\Commands;

use Illuminate\Filesystem\Filesystem;
use Alexeykhr\ClickhouseMigrations\Tests\TestCase;
use Alexeykhr\ClickhouseMigrations\Stubs\MigrationStub;
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
    public function testMakeSimpleMigration(): void
    {
        $this->artisan('make:clickhouse-migration create_users_table');

        $this->assertClickhouseMigrationFile('users');
    }
}
