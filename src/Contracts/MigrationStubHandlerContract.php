<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Contracts;

interface MigrationStubHandlerContract
{
    /**
     * @param  string  $content
     * @param  array  $parameters
     * @return string
     */
    public function populate(string $content, array $parameters): string;
}
