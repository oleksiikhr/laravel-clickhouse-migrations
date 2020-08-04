<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Alexeykhr\ClickhouseMigrations\Factories\FactoryStub;
use Alexeykhr\ClickhouseMigrations\Concerns\MigrationPath;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Alexeykhr\ClickhouseMigrations\Migrations\MigrationCreator;
use Alexeykhr\ClickhouseMigrations\Exceptions\ClickhouseStubException;

class MigrateMakeCommand extends Command
{
    use MigrationPath;

    /**
     * @inheritDoc
     */
    protected $signature = 'make:clickhouse-migration {name : The name of the migration}
                {--stub= : Use a specific stub}
                {--table= : The table to migrate}
                {--path= : Path to Clickhouse directory with migrations}
                {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}';

    /**
     * @inheritDoc
     */
    protected $description = 'Create a new ClickHouse migration file';

    /**
     * Execute the console command
     *
     * @param  MigrationCreator  $creator
     * @param  Composer  $composer
     * @return void
     * @throws ClickhouseStubException
     * @throws FileNotFoundException
     */
    public function handle(MigrationCreator $creator, Composer $composer): void
    {
        // Depending on the received parameters, we use the appropriate stub
        // to generate the migration
        $this->applyStub($creator);

        $path = $creator->create(
            $this->getNameArgument(),
            $this->getMigrationPath(),
            ['table' => $this->getTableOption()]
        );

        $this->line($path
            ? "<info>Migration created</info> {$path}."
            : '<error>Migration file not created.</error>');

        $composer->dumpAutoloads();
    }

    /**
     * Use stub file to generate migration
     *
     * @param  MigrationCreator  $creator
     * @return void
     * @throws ClickhouseStubException
     */
    protected function applyStub(MigrationCreator $creator): void
    {
        if ($stub = $this->option('stub')) {
            $creator->setStub(FactoryStub::create($stub));
        } elseif ($this->getTableOption()) {
            $creator->setStub(FactoryStub::create('table'));
        }
    }

    /**
     * @return string
     */
    protected function getNameArgument(): string
    {
        return Str::snake(trim($this->argument('name')));
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
}
