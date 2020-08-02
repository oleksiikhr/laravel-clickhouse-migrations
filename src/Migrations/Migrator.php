<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Migrations;

use Illuminate\Support\Str;
use Illuminate\Console\OutputStyle;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Alexeykhr\ClickhouseMigrations\Contracts\ClickhouseMigrationContract;

class Migrator
{
    /**
     * @var MigrationRepository
     */
    protected $repository;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $migrationPath = 'database/clickhouse-migrations';

    /**
     * @var OutputStyle|null
     */
    protected $output;

    public function __construct(MigrationRepository $model, Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->repository = $model;
    }

    /**
     * @param  int  $step
     * @return void
     */
    public function runUp(int $step): void
    {
        $migrations = $this->getMigrationsUp();

        if (! $migrations->valid()) {
            $this->log("<info>Migrations are empty.</info>");
            return;
        }

        $nextBatch = $this->repository->getNextBatchNumber();

        for ($i = $step; ($i > 0 || $step === 0) && $migrations->valid(); $i--) {
            $this->filesystem->requireOnce($migrations->current());

            $startTime = microtime(true);

            $migrationName = $this->getMigrationName($migrations->current());
            $this->resolve($migrationName)->up();

            $runTime = round(microtime(true) - $startTime, 2);

            $this->log("<info>Completed in {$runTime} seconds</info> {$migrationName}");

            $this->repository->add($migrationName, $nextBatch);

            $migrations->next();
        }
    }

    /**
     * @return \Generator
     */
    public function getMigrationsUp(): \Generator
    {
        $files = $this->unAppliedMigrations();

        foreach ($files as $file) {
            yield $this->migrationPath.'/'.$file->getFilename();
        }
    }

    /**
     * @param  int  $step
     * @return void
     */
    public function runDown(int $step): void
    {
        $migrations = $this->getMigrationsDown();

        if (! $migrations->valid()) {
            $this->log("<info>Migrations are empty.</info>");
            return;
        }

        for ($i = $step; ($i > 0 || $step === 0) && $migrations->valid(); $i--) {
            if (! $this->filesystem->exists($migrations->current())) {
                throw new \RuntimeException('File not exists '.$migrations->current());
            }

            $this->filesystem->requireOnce($migrations->current());

            $startTime = microtime(true);

            $migrationName = $this->getMigrationName($migrations->current());
            $this->resolve($migrationName)->down();

            $runTime = round(microtime(true) - $startTime, 2);

            $this->log("<info>Completed in {$runTime} seconds</info> {$migrationName}");

            $this->repository->delete($migrationName);

            $migrations->next();
        }
    }

    /**
     * @return \Generator
     */
    public function getMigrationsDown(): \Generator
    {
        $migrations = $this->repository->latest();

        foreach ($migrations as $migration) {
            yield $this->migrationPath.'/'.$migration.'.php';
        }
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
     * @return $this
     */
    public function ensureTableExists(): self
    {
        if (! $this->repository->exists()) {
            $this->repository->create();
        }

        return $this;
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
     * @return MigrationRepository
     */
    public function getRepository(): MigrationRepository
    {
        return $this->repository;
    }

    /**
     * @param  MigrationRepository  $repository
     * @return $this
     */
    public function setRepository(MigrationRepository $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * @param  Filesystem  $filesystem
     * @return $this
     */
    public function setFilesystem(Filesystem $filesystem): self
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    /**
     * @return string
     */
    public function getMigrationPath(): string
    {
        return $this->migrationPath;
    }

    /**
     * @param  string  $migrationPath
     * @return $this
     */
    public function setMigrationPath(string $migrationPath): self
    {
        $this->migrationPath = $migrationPath;

        return $this;
    }

    /**
     * @return SplFileInfo[]
     */
    protected function unAppliedMigrations(): array
    {
        $files = $this->filesystem->files($this->migrationPath);

        return $this->pendingMigrations($files, $this->repository->all());
    }

    /**
     * @param  SplFileInfo[]  $files
     * @param  array  $migrations
     * @return SplFileInfo[]
     */
    protected function pendingMigrations(array $files, array $migrations): array
    {
        return collect($files)
            ->reject(function ($file) use ($migrations) {
                $name = $this->getMigrationName($file->getFilename());

                return in_array($name, $migrations, true);
            })->all();
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
