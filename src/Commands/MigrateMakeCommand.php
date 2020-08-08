<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Alexeykhr\ClickhouseMigrations\Factories\FactoryStub;
use Alexeykhr\ClickhouseMigrations\Concerns\MigrationPath;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Alexeykhr\ClickhouseMigrations\Concerns\MigrationStubHandler;
use Alexeykhr\ClickhouseMigrations\Exceptions\ClickhouseStubException;
use Alexeykhr\ClickhouseMigrations\Contracts\MigrationCreatorContract;

class MigrateMakeCommand extends Command
{
    use MigrationPath, MigrationStubHandler;

    /**
     * @inheritDoc
     */
    protected $signature = 'make:clickhouse-migration {name : The name of the migration}
                {--stub= : Use a specific stub}
                {--stub.param=* : Parameter data for stub Handlers in key:value format}
                {--stub.handler=* : Use additional handlers for Stub file}
                {--table= : The table to migrate}
                {--path= : Path to Clickhouse directory with migrations}
                {--realpath : Indicate any provided file paths are pre-resolved absolute paths}';

    /**
     * @inheritDoc
     */
    protected $description = 'Create a new ClickHouse migration file';

    /**
     * Execute the console command
     *
     * @param  MigrationCreatorContract  $creator
     * @param  Composer  $composer
     * @return void
     * @throws ClickhouseStubException
     * @throws FileNotFoundException
     */
    public function handle(MigrationCreatorContract $creator, Composer $composer): void
    {
        $creator->getStub()->setHandlers($this->getStubHandlers());

        $path = $creator->create(
            $this->getStubFile(),
            $this->getNameArgument(),
            $this->getMigrationPath(),
            $this->getStubParameters()
        );

        $this->line($path
            ? "<info>Migration created</info> {$path}."
            : '<error>Migration file not created.</error>');

        $composer->dumpAutoloads();
    }

    /**
     * @return string
     * @throws ClickhouseStubException
     */
    protected function getStubFile(): string
    {
        if ($stub = $this->option('stub')) {
            return FactoryStub::make($stub);
        }

        if ($this->getTableOption()) {
            return FactoryStub::make('table');
        }

        return FactoryStub::make();
    }

    /**
     * @return string
     */
    protected function getNameArgument(): string
    {
        return trim($this->argument('name'));
    }

    /**
     * @return string|null
     */
    protected function getTableOption(): ?string
    {
        if ($table = $this->option('table')) {
            return trim($table);
        }

        return null;
    }

    /**
     * @return array
     */
    protected function getStubParameters(): array
    {
        return array_merge(
            $this->getDefaultStubParameters(),
            $this->getOptionStubParameters()
        );
    }

    /**
     * @return array
     */
    protected function getDefaultStubParameters(): array
    {
        $parameters = [];

        if ($table = $this->getTableOption()) {
            $parameters['table'] = $table;
        }

        return $parameters;
    }

    /**
     * @return array
     */
    protected function getOptionStubParameters(): array
    {
        $optionParameters = $this->option('stub.param');
        $parameters = [];

        foreach ($optionParameters as $optionParameter) {
            [$key, $value] = explode(":", $optionParameter);

            $parameters[$key] = $value;
        }

        return $parameters;
    }
}
