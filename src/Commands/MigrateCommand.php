<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Alexeykhr\ClickhouseMigrations\Migrations\Migrator;
use Alexeykhr\ClickhouseMigrations\Concerns\MigrationPath;

class MigrateCommand extends Command
{
    use ConfirmableTrait;

    /**
     * @inheritDoc
     */
    protected $signature = 'clickhouse-migrate
                {--force : Force the operation to run when in production}';

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

        $migrator->setOutput($this->getOutput());
        $migrator->setUp();

        return 0;
    }
}
