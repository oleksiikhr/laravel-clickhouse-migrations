<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Migrations;

use Illuminate\Support\Str;
use Illuminate\Console\OutputStyle;
use Illuminate\Filesystem\Filesystem;
use Alexeykhr\ClickhouseMigrations\Contracts\MigratorActionContract;
use Alexeykhr\ClickhouseMigrations\Contracts\ClickhouseMigrationContract;

class Migrator
{
    /**
     * @var MigrationModel
     */
    protected $model;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var OutputStyle|null
     */
    protected $output;

    public function __construct(MigrationModel $model, Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->model = $model;
    }

    /**
     * @param  MigratorActionContract  $action
     * @param  int  $step
     * @return void
     */
    public function run(MigratorActionContract $action, int $step): void
    {
        $this->ensureTableExists();

        $action->setMigrator($this);

        $files = $action->getMigrations($step);

        if (! $files->valid()) {
            $this->log("<info>Migrations are empty.</info>");
            return;
        }

        for ($i = $step; ($i > 0 || $step === 0) && $files->valid(); $i--) {
            $this->filesystem->requireOnce($files->current());

            $startTime = microtime(true);

            $name = $this->getMigrationName($files->current());
            $action->run($this->resolve($name));

            $runTime = round(microtime(true) - $startTime, 2);

            $this->log("<info>Completed in {$runTime} seconds</info> {$files->current()}");

            $action->complete($name);

            $files->next();
        }
    }

    /**
     * @param  OutputStyle  $output
     * @return $this
     */
    public function setOutput(OutputStyle $output): self
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Get the name of the migration
     *
     * @param  string  $path
     * @return string
     */
    public function getMigrationName(string $path): string
    {
        return str_replace('.php', '', basename($path));
    }

    /**
     * @return MigrationModel
     */
    public function getModel(): MigrationModel
    {
        return $this->model;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * @return void
     */
    protected function ensureTableExists(): void
    {
        if (! $this->model->exists()) {
            $this->model->create();
        }
    }

    /**
     * @param  string  $message
     * @return void
     */
    protected function log(string $message): void
    {
        if (! $this->output) {
            return;
        }

        $this->output->writeln($message);
    }

    /**
     * Resolve a migration instance from a file
     *
     * @param  string  $path
     * @return ClickhouseMigrationContract
     */
    protected function resolve(string $path): ClickhouseMigrationContract
    {
        $class = Str::studly(implode('_', array_slice(explode('_', $path), 4)));

        return app($class);
    }
}
