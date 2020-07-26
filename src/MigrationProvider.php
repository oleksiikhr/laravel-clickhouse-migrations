<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations;

use Illuminate\Support\ServiceProvider;
use Alexeykhr\ClickhouseMigrations\Commands\MigrateCommand;
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
