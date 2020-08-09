<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Migrations;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Alexeykhr\ClickhouseMigrations\Contracts\MigrationStubContract;
use Alexeykhr\ClickhouseMigrations\Contracts\MigrationCreatorContract;

class MigrationCreator implements MigrationCreatorContract
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var MigrationStubContract
     */
    protected $stub;

    public function __construct(Filesystem $filesystem, MigrationStubContract $stub)
    {
        $this->filesystem = $filesystem;
        $this->stub = $stub;
    }

    /**
     * @inheritDoc
     */
    public function create(string $stubPath, string $fileName, string $migrationPath, array $parameters = []): ?string
    {
        $this->filesystem->ensureDirectoryExists($migrationPath);

        $path = $this->generatePath(Str::snake($fileName), $migrationPath);

        $content = $this->stub->generate($fileName, $stubPath, $parameters);

        return $this->filesystem->put($path, $content) === false
            ? null
            : $path;
    }

    /**
     * @inheritDoc
     */
    public function getStub(): MigrationStubContract
    {
        return $this->stub;
    }

    /**
     * @inheritDoc
     */
    public function setStub(MigrationStubContract $stub): MigrationCreatorContract
    {
        $this->stub = $stub;

        return $this;
    }

    /**
     * Get the full path to the migration
     *
     * @param  string  $name
     * @param  string  $directory
     * @return string
     */
    protected function generatePath(string $name, string $directory): string
    {
        return $directory.'/'.$this->getDatePrefix().'_'.Str::snake($name).'.php';
    }

    /**
     * Get the date prefix for the migration
     *
     * @return string
     */
    protected function getDatePrefix(): string
    {
        return date('Y_m_d_His');
    }
}
