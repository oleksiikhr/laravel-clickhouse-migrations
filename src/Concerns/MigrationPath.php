<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Concerns;

trait MigrationPath
{
    /**
     * @return string
     */
    public function getMigrationPath(): string
    {
        if ($path = $this->option('path')) {
            return $this->usingRealPath()
                ? $path
                : $this->getLaravel()->basePath().'/'.$path;
        }

        return $this->defaultMigrationPath();
    }

    /**
     * @return bool
     */
    protected function usingRealPath(): bool
    {
        return $this->hasOption('realpath') && $this->option('realpath');
    }

    /**
     * @return string
     */
    protected function defaultMigrationPath(): string
    {
        return config('clickhouse.migrations.path');
    }
}
