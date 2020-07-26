<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Concerns;

trait MigrationPath
{
    /**
     * @return string
     */
    public function getMigrationPath(): string
    {
        return $this->option('path') ?: config('clickhouse.migrations.path');
    }
}
