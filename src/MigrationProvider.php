<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Alexeykhr\ClickhouseMigrations\Migrations\Migrator;
use Alexeykhr\ClickhouseMigrations\Commands\MigrateCommand;
use Alexeykhr\ClickhouseMigrations\Migrations\MigrationModel;
use Alexeykhr\ClickhouseMigrations\Commands\MigrateMakeCommand;
use Alexeykhr\ClickhouseMigrations\Commands\MigrateStubCommand;
use Alexeykhr\ClickhouseMigrations\Commands\MigrateRollbackCommand;

class MigrationProvider extends ServiceProvider
{
    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->app->singleton(Clickhouse::class, static function ($app, array $config = []) {
            return new Clickhouse($config);
        });

        $this->app->bind(Migrator::class, static function ($app, array $parameters = []) {
            $client = $parameters['client'] ?? app(Clickhouse::class)->getClient();
            $table = $parameters['table'] ?? config('clickhouse.migrations.table');
            $path = $parameters['path'] ?? config('clickhouse.migrations.path');
            $filesystem = $parameters['filesystem'] ?? app(Filesystem::class);

            $model = new MigrationModel($table, $client);

            return new Migrator($path, $model, $filesystem);
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
                MigrateStubCommand::class,
                MigrateRollbackCommand::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../config/clickhouse.php' => config_path('clickhouse.php'),
        ], 'config');
    }
}
