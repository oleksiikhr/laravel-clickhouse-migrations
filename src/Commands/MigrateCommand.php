<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Alexeykhr\ClickhouseMigrations\Migrations\Migrator;
use Alexeykhr\ClickhouseMigrations\Concerns\MigrationPath;
use Alexeykhr\ClickhouseMigrations\Concerns\MigrationStep;
use Alexeykhr\ClickhouseMigrations\Concerns\MigrationOutput;

class MigrateCommand extends Command
{
    use ConfirmableTrait, MigrationPath, MigrationStep, MigrationOutput;

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

        $this->migrator->runUp($this->getStep());
    }

    /**
     * @return bool
     */
    protected function prepare(): bool
    {
        $this->migrator->ensureTableExists()
            ->setOutput($this->getOutput())
            ->setMigrationPath($this->getMigrationPath());

        $migrations = $this->migrator->getMigrationsUp();

        if (! $migrations->valid()) {
            $this->output->writeln('<info>Migrations are empty!</info>');
            return false;
        }

        $this->outputMigrations('Migrations Up', $migrations);
        return true;
    }
}
