<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Contracts;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

interface MigrationStubContract
{
    /**
     * @param  string  $fileName
     * @param  string  $stubPath
     * @param  array  $parameters
     * @return string
     * @throws FileNotFoundException
     */
    public function generate(string $fileName, string $stubPath, array $parameters = []): string;
}
