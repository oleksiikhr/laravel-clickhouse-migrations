<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Concerns;

trait MigrationOutput
{
    /**
     * @param  string  $title
     * @param  \Generator  $migrations
     * @return void
     */
    protected function outputMigrations(string $title, \Generator $migrations): void
    {
        $this->output->newLine();
        $this->output->writeln(" <info>  {$title}:</info>");
        $this->output->newLine();

        $totalSteps = $this->getStep();
        $step = 0;

        foreach ($migrations as $migration) {
            if ($totalSteps === 0 || $totalSteps > $step) {
                $this->output->writeln(' * '.$this->migrator->getMigrationName($migration));

                $step++;
            }
        }

        $this->output->newLine();
    }
}
