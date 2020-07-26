<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Alexeykhr\ClickhouseMigrations\StubFactory;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Alexeykhr\ClickhouseMigrations\Migrations\MigrationCreator;
use Alexeykhr\ClickhouseMigrations\Exceptions\ClickhouseStubException;

class MigrateMakeCommand extends Command
{
    /**
     * @inheritDoc
     */
    protected $signature = 'make:clickhouse-migration {name : The name of the migration}
                {--table= : The table to migrate}';

    /**
     * @inheritDoc
     */
    protected $description = 'Create a new ClickHouse migration file';

    /**
     * @var MigrationCreator
     */
    protected $creator;

    /**
     * @var Composer
     */
    protected $composer;

    public function __construct(MigrationCreator $creator, Composer $composer)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = $composer;
    }

    /**
     * Execute the console command
     *
     * @return void
     * @throws FileNotFoundException
     * @throws ClickhouseStubException
     */
    public function handle(): void
    {
        if ($table = $this->getTableOption()) {
            $this->creator->setStub(StubFactory::create('table'));
        }

        $path = $this->creator->create(
            $this->getNameArgument(),
            $this->getMigrationPath(),
            $table
        );

        $this->line($path
            ? "<info>Migration created {$path}</info>"
            : '<error>Something went wrong</error>');

        $this->composer->dumpAutoloads();
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

    /**
     * @return string
     */
    protected function getNameArgument(): string
    {
        return Str::snake(trim($this->input->getArgument('name')));
    }

    /**
     * @return string|null
     */
    protected function getTableOption(): ?string
    {
        return $this->option('table');
    }
}
