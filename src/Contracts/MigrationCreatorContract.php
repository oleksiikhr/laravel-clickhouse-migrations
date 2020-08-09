<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Contracts;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

interface MigrationCreatorContract
{
    /**
     * Create a new migration from stub file
     *
     * @param  string  $stubPath
     * @param  string  $fileName
     * @param  string  $migrationPath
     * @param  array  $parameters
     * @return string|null path to file
     * @throws FileNotFoundException
     */
    public function create(string $stubPath, string $fileName, string $migrationPath, array $parameters = []): ?string;

    /**
     * @return MigrationStubContract
     */
    public function getStub(): MigrationStubContract;

    /**
     * @param  MigrationStubContract  $stub
     * @return $this
     */
    public function setStub(MigrationStubContract $stub): self;
}
