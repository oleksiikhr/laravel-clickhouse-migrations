<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Migrations;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Alexeykhr\ClickhouseMigrations\Contracts\ClickhouseMigrationContract;

class Migrator
{
    /**
     * @var string
     */
    protected $migrationPath;

    /**
     * @var MigrationModel
     */
    protected $model;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(string $migrationPath, MigrationModel $model, Filesystem $filesystem)
    {
        $this->migrationPath = $migrationPath;
        $this->model = $model;
        $this->filesystem = $filesystem;
    }

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->ensureTableExists();

        $paths = $this->getMigrationsForUp();

        if (empty($paths)) {
            // TODO Console output
            return;
        }

        $nextBatch = $this->model->getNextBatchNumber();

        foreach ($paths as $path) {
            $this->runUpMigration($path, $nextBatch);
        }
    }

    /**
     * @return void
     */
    public function setDown(): void
    {
        $this->ensureTableExists();

        $paths = $this->getMigrationsForDown();

        if (empty($paths)) {
            // TODO Console output
            return;
        }

//        $batch = $this->model->getLastBatchNumber();

        foreach ($paths as $path) {
            $this->runDownMigration($path);
        }
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
     * @param  string  $path
     * @return void
     */
    protected function runDownMigration(string $path): void
    {
        $migration = $this->resolve($path);

        $startTime = microtime(true);

        $migration->down();

        $runTime = round(microtime(true) - $startTime, 2);

        $this->model->delete($path);

        dump($path.': '.$runTime); // TODO Console output
    }

    /**
     * @param  string  $path
     * @param  int  $batch
     * @return void
     */
    protected function runUpMigration(string $path, int $batch): void
    {
        $migration = $this->resolve($path);

        $startTime = microtime(true);

        $migration->up();

        $runTime = round(microtime(true) - $startTime, 2);

        $this->model->add($path, $batch);

        dump($path.': '.$runTime); // TODO Console output
    }

    /**
     * @return string[]
     */
    protected function getMigrationsForUp(): array
    {
        $files = $this->filesystem->files($this->migrationPath);
        $files = $this->pendingMigrations($files, $this->model->all());

        $migrations = [];

        foreach ($files as $file) {
            $this->filesystem->requireOnce($file);

            $migrations[] = $this->getMigrationName($file->getFilename());
        }

        return $migrations;
    }

    /**
     * @return string[]
     */
    protected function getMigrationsForDown(): array
    {
        $migrations = $this->model->getLast();

        foreach ($migrations as $migration) {
            $file = $this->migrationPath.'/'.$migration.'.php';

            if (! $this->filesystem->exists($file)) {
                throw new \RuntimeException('File not exists '.$file);
            }

            $this->filesystem->requireOnce($file);
        }

        return $migrations;
    }

    /**
     * @param  array  $files
     * @param  array  $migrations
     * @return array
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
     * Get the name of the migration
     *
     * @param  string  $path
     * @return string
     */
    protected function getMigrationName(string $path): string
    {
        return str_replace('.php', '', basename($path));
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
