<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Commands;

use Illuminate\Console\Command;

class MigrateCommand extends Command
{
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
     * @return void
     */
    public function handle(): void
    {
        dd('clickhouse-migrate');
    }
}
