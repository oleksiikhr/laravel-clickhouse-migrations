<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Contracts;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

interface ClickhouseStubContract
{
    /**
     * @param  string  $className
     * @param  array  $parameters
     * @return string
     * @throws FileNotFoundException
     */
    public function generate(string $className, array $parameters = []): string;

    /**
     * @return string
     */
    public function getFilename(): string;
}
