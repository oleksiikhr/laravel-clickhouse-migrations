<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\ConfirmableTrait;
use Alexeykhr\ClickhouseMigrations\Clickhouse;
use Alexeykhr\ClickhouseMigrations\Migrations\Migrator;
use Alexeykhr\ClickhouseMigrations\Migrations\MigrationModel;

class MigrateRollbackCommand extends Command
{
    use ConfirmableTrait;

    /**
     * @inheritDoc
     */
    protected $signature = 'clickhouse-migrate:rollback
                {--force : Force the operation to run when in production}';

    /**
     * @inheritDoc
     */
    protected $description = 'Downgrade the ClickHouse database migrations';

    /**
     * Execute the console command
     *
     * @param  Clickhouse  $clickhouse
     * @param  Filesystem  $filesystem
     * @return int
     */
    public function handle(Clickhouse $clickhouse, Filesystem $filesystem): int
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        $model = new MigrationModel(config('clickhouse.migrations.table'), $clickhouse->getClient());

        $migrator = new Migrator($this->getMigrationPath(), $model, $filesystem);
        $migrator->setDown();

        return 0;
    }

    /**
     * Get the path to the migration directory
     *
     * @return string
     */
    protected function getMigrationPath(): string
    {
        return $this->laravel->databasePath().'/clickhouse-migrations';
    }
}
