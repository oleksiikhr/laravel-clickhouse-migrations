<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Alexeykhr\ClickhouseMigrations\Migrations\Migrator;

class MigrateRollbackCommand extends Command
{
    use ConfirmableTrait;

    /**
     * @inheritDoc
     */
    protected $signature = 'clickhouse-migrate:rollback
                {--force : Force the operation to run when in production}
                {--path= : Path to Clickhouse directory with migrations}';

    /**
     * @inheritDoc
     */
    protected $description = 'Downgrade the ClickHouse database migrations';

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
        $migrator->setDown();

        return 0;
    }
}
