<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Migrations;

use Generator;
use Alexeykhr\ClickhouseMigrations\Contracts\MigratorActionContract;
use Alexeykhr\ClickhouseMigrations\Contracts\ClickhouseMigrationContract;

class MigratorActionDown implements MigratorActionContract
{
    /**
     * @var Migrator
     */
    protected $migrator;

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
    }

    /**
     * @inheritDoc
     */
    public function getMigrations(int $step): Generator
    {
        $migrations = $this->getClickhouseMigrations();

        foreach ($migrations as $migration) {
            $file = $this->migrationPath.'/'.$migration.'.php';

            if (! $this->migrator->getFilesystem()->exists($file)) {
                throw new \RuntimeException('File not exists '.$file);
            }

            yield $file;
        }
    }

    /**
     * @inheritDoc
     */
    public function run(ClickhouseMigrationContract $migration): void
    {
        $migration->down();
    }

    /**
     * @inheritDoc
     */
    public function complete(string $fileName): void
    {
        $this->migrator->getModel()->delete($fileName);
    }

    /**
     * @return array
     */
    protected function getClickhouseMigrations(): array
    {
        return $this->migrator->getModel()->getLast();
    }
}
