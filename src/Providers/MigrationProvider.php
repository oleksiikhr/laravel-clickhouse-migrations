<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Alexeykhr\ClickhouseMigrations\Clickhouse;
use Alexeykhr\ClickhouseMigrations\Migrations\Migrator;
use Alexeykhr\ClickhouseMigrations\Stubs\MigrationStub;
use Alexeykhr\ClickhouseMigrations\Commands\MigrateCommand;
use Alexeykhr\ClickhouseMigrations\Commands\MigrateMakeCommand;
use Alexeykhr\ClickhouseMigrations\Migrations\MigrationCreator;
use Alexeykhr\ClickhouseMigrations\Migrations\MigrationRepository;
use Alexeykhr\ClickhouseMigrations\Commands\MigrateRollbackCommand;
use Alexeykhr\ClickhouseMigrations\Contracts\MigrationCreatorContract;

class MigrationProvider extends ServiceProvider
{
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->app->singleton('clickhouse', static function ($app, array $config = []) {
            $clickhouse = new Clickhouse($config);

            return $clickhouse->getClient();
        });

        $this->app->bind(Migrator::class, static function ($app, array $parameters = []) {
            $client = $parameters['client'] ?? app('clickhouse');
            $table = $parameters['table'] ?? config('clickhouse.migrations.table');
            $filesystem = $parameters['filesystem'] ?? app(Filesystem::class);

            $repository = new MigrationRepository($table, $client);

            return new Migrator($repository, $filesystem);
        });

        $this->app->bind(MigrationCreatorContract::class, static function ($app, array $parameters = []) {
            $filesystem = $parameters['filesystem'] ?? app(Filesystem::class);
            $stub = $parameters['stub'] ?? app(MigrationStub::class);

            return new MigrationCreator($filesystem, $stub);
        });
    }

    /**
     * Bootstrap services
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrateCommand::class,
                MigrateMakeCommand::class,
                MigrateRollbackCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../../config/clickhouse.php' => config_path('clickhouse.php'),
            ], 'config');
        }
    }
}
