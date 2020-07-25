<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Commands;

use Illuminate\Console\Command;
use Alexeykhr\ClickhouseMigrations\StubFactory;
use Alexeykhr\ClickhouseMigrations\Contracts\ClickhouseStubContract;
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
     * @return ClickhouseStubContract[]
     * @throws ClickhouseStubException
     */
    protected function getStubs(): array
    {
        if ($type = $this->option('type')) {
            return [$type => StubFactory::create($type)];
        }

        return collect(StubFactory::getExistsStubs())->map(static function ($stub) {
            return app($stub);
        })->toArray();
    }

    /**
     * @param  ClickhouseStubContract  $stub
     * @return void
     */
    protected function generate(ClickhouseStubContract $stub): void
    {
        if ($path = $stub->publish()) {
            $this->line("<info>Publishing complete {$path}</info>");
        } else {
            $this->line('<error>Something went wrong</error>');
        }
    }
}
