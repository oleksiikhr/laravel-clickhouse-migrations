<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Alexeykhr\ClickhouseMigrations\Migrations\Migrator;
use Alexeykhr\ClickhouseMigrations\Concerns\MigrationPath;
use Alexeykhr\ClickhouseMigrations\Concerns\MigrationStep;
use Alexeykhr\ClickhouseMigrations\Migrations\Actions\MigratorActionUp;

class MigrateCommand extends Command
{
    use ConfirmableTrait, MigrationPath, MigrationStep;

    /**
     * @inheritDoc
     */
    protected $signature = 'clickhouse-migrate
                {--force : Force the operation to run when in production}
                {--path= : Path to Clickhouse directory with migrations}
                {--step= : Number of migrations to rollback}';

    /**
     * @inheritDoc
     */
    protected $description = 'Run the ClickHouse database migrations';

    /**
     * Execute the console command
     *
     * @param  Migrator  $migrator
     * @return int
     */
    public function handle(Migrator $migrator): int
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        $action = new MigratorActionUp($this->getMigrationPath());

        $migrator->setOutput($this->getOutput());
        $migrator->run($action, $this->getStep());

        return 0;
    }
}
