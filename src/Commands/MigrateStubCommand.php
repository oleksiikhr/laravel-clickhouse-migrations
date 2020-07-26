<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Commands;

use Illuminate\Console\Command;
use Alexeykhr\ClickhouseMigrations\StubFactory;
use Alexeykhr\ClickhouseMigrations\Contracts\MigrationStubContract;
use Alexeykhr\ClickhouseMigrations\Exceptions\ClickhouseStubException;

class MigrateStubCommand extends Command
{
    /**
     * @inheritDoc
     */
    protected $signature = 'clickhouse-migrate:stub
                {--type= : Publish a specific type of stub}';

    /**
     * @inheritDoc
     */
    protected $description = 'Publish all stubs that are available for customization';

    /**
     * Execute the console command
     *
     * @return void
     * @throws ClickhouseStubException
     */
    public function handle(): void
    {
        $stubs = $this->getStubs();

        foreach ($stubs as $stub) {
            $this->generate($stub);
        }
    }

    /**
     * @return MigrationStubContract[]
     * @throws ClickhouseStubException
     */
    protected function getStubs(): array
    {
        if ($type = $this->option('type')) {
            return [$type => StubFactory::create($type)];
        }

        $stubs = StubFactory::getExistsStubs();

        return collect($stubs)->map(static function ($stub) {
            return app($stub);
        })->all();
    }

    /**
     * @param  MigrationStubContract  $stub
     * @return void
     */
    protected function generate(MigrationStubContract $stub): void
    {
        if ($path = $stub->publish()) {
            $this->line("<info>Publishing complete</info> {$path}.");
        } else {
            $this->line('<error>Something went wrong.</error>');
        }
    }
}
