<?php

declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Concerns;

trait MigrationStep
{
    /**
     * @return int
     */
    public function getStep(): int
    {
        return (int) $this->option('step');
    }
}
