<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Alexeykhr\ClickhouseMigrations\Migrations\Migrator;
use Alexeykhr\ClickhouseMigrations\Commands\MigrateCommand;
use Alexeykhr\ClickhouseMigrations\Commands\MigrateMakeCommand;
use Alexeykhr\ClickhouseMigrations\Commands\MigrateStubCommand;
use Alexeykhr\ClickhouseMigrations\Migrations\MigrationRepository;
use Alexeykhr\ClickhouseMigrations\Commands\MigrateRollbackCommand;

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

            $model = new MigrationRepository($table, $client);

            return new Migrator($model, $filesystem);
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
