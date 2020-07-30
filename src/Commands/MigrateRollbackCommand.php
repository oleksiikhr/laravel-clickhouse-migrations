<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Alexeykhr\ClickhouseMigrations\Migrations\Migrator;
use Alexeykhr\ClickhouseMigrations\Concerns\MigrationPath;
use Alexeykhr\ClickhouseMigrations\Concerns\MigrationStep;
use Alexeykhr\ClickhouseMigrations\Concerns\MigrationOutput;

class MigrateRollbackCommand extends Command
{
    use ConfirmableTrait, MigrationPath, MigrationStep, MigrationOutput;

    /**
     * @inheritDoc
     */
    protected $signature = 'clickhouse-migrate:rollback
                {--force : Force the operation to run when in production}
                {--path= : Path to Clickhouse directory with migrations}
                {--step=1 : Number of migrations to rollback}';

    /**
     * @inheritDoc
     */
    protected $description = 'Rollback the ClickHouse database migrations';

    /**
     * @var Migrator
     */
    protected $migrator;

    public function __construct(Migrator $migrator)
    {
        parent::__construct();

        $this->migrator = $migrator;
    }

    /**
     * Execute the console command
     *
     * @return void
     */
    public function handle(): void
    {
        if (! $this->prepare() || ! $this->confirmToProceed()) {
            return;
        }

        $this->migrator->runDown($this->getStep());
    }

    /**
     * @return bool
     */
    protected function prepare(): bool
    {
        $this->migrator->ensureTableExists()
            ->setOutput($this->getOutput())
            ->setMigrationPath($this->getMigrationPath());

        $migrations = $this->migrator->getMigrationsDown();

        if (! $migrations->valid()) {
            $this->output->writeln('<info>Migrations are empty!</info>');
            return false;
        }

        $this->outputMigrations('Migrations Down', $migrations);
        return true;
    }
}
