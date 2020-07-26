<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Contracts;

use Generator;
use Alexeykhr\ClickhouseMigrations\Migrations\Migrator;

interface MigratorActionContract
{
    public function __construct(string $migrationPath);

    /**
     * @param  Migrator  $migrator
     * @return void
     */
    public function setMigrator(Migrator $migrator): void;

    /**
     * @param  ClickhouseMigrationContract  $migration
     * @return void
     */
    public function run(ClickhouseMigrationContract $migration): void;

    /**
     * @param  string  $fileName
     * @return void
     */
    public function complete(string $fileName): void;

    /**
     * @param  int  $step
     * @return Generator
     */
    public function getMigrations(int $step): Generator;
}
