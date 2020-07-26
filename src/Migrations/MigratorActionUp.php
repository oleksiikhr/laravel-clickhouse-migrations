<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Migrations;

use Generator;
use Alexeykhr\ClickhouseMigrations\Contracts\MigratorActionContract;
use Alexeykhr\ClickhouseMigrations\Contracts\ClickhouseMigrationContract;
use Symfony\Component\Finder\SplFileInfo;

class MigratorActionUp implements MigratorActionContract
{
    /**
     * @var Migrator
     */
    protected $migrator;

    /**
     * @var int
     */
    protected $nextBatch;

    /**
     * @var string
     */
    protected $migrationPath;

    public function __construct(string $migrationPath)
    {
        $this->migrationPath = $migrationPath;
    }

    /**
     * @inheritDoc
     */
    public function setMigrator(Migrator $migrator): void
    {
        $this->migrator = $migrator;
        $this->nextBatch = $migrator->getModel()->getNextBatchNumber();
    }

    /**
     * @inheritDoc
     */
    public function getMigrations(int $step): Generator
    {
        $files = $this->migrator->getFilesystem()->files($this->migrationPath);
        $files = $this->pendingMigrations($files, $this->getClickhouseMigrations());

        foreach ($files as $file) {
            yield $this->migrationPath.'/'.$file->getFilename();
        }
    }

    /**
     * @inheritDoc
     */
    public function run(ClickhouseMigrationContract $migration): void
    {
        $migration->up();
    }

    /**
     * @inheritDoc
     */
    public function complete(string $fileName): void
    {
        $this->migrator->getModel()->add($fileName, $this->nextBatch);
    }

    /**
     * @return array
     */
    protected function getClickhouseMigrations(): array
    {
        return $this->migrator->getModel()->all();
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
                $name = $this->migrator->getMigrationName($file->getFilename());

                return in_array($name, $migrations, true);
            })->all();
    }
}
