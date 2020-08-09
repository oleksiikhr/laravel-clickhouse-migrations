<?php

declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Concerns;

use Generator;
use Alexeykhr\ClickhouseMigrations\Migrations\Migrator;

trait MigrationOutput
{
    /**
     * @var Migrator
     */
    protected $migrator;

    /**
     * @param  Generator  $migrations
     * @return bool
     */
    public function outputMigrations(Generator $migrations): bool
    {
        if (! $this->option('output')) {
            return true;
        }

        $this->outputWriteMigrations($migrations);

        if ($this->hasOption('force') && $this->option('force')) {
            return true;
        }

        $confirm = $this->confirm('Apply migrations?');

        if (! $confirm) {
            $this->comment('Command Canceled!');

            return false;
        }

        return true;
    }

    /**
     * @param  Generator  $migrations
     * @return void
     */
    protected function outputWriteMigrations(Generator $migrations): void
    {
        $this->output->newLine();
        $this->output->writeln('<info>   Migrations for execution:</info>');

        $totalSteps = $this->getStep();
        $step = 0;

        foreach ($migrations as $migration) {
            if ($totalSteps === 0 || $totalSteps > $step) {
                $this->output->writeln(' - '.$this->migrator->getMigrationName($migration));

                $step++;
            }
        }

        $this->output->newLine();
    }
}
