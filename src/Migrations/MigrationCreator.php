<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Migrations;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Alexeykhr\ClickhouseMigrations\Stubs\MigrationStub;

class MigrationCreator
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var MigrationStub
     */
    protected $stub;

    public function __construct(Filesystem $filesystem, MigrationStub $stub)
    {
        $this->filesystem = $filesystem;
        $this->stub = $stub;
    }

    /**
     * Create a new migration from stub file
     *
     * @param  string  $stubFile
     * @param  string  $fileName
     * @param  string  $directory
     * @param  array  $parameters
     * @return string|null path to file
     * @throws FileNotFoundException
     */
    public function create(string $stubFile, string $fileName, string $directory, array $parameters = []): ?string
    {
        $this->filesystem->ensureDirectoryExists($directory);

        $path = $this->generatePath($fileName, $directory);

        $content = $this->stub->generate($stubFile, $parameters);

        return $this->filesystem->put($path, $content) === false
            ? null
            : $path;
    }

    /**
     * @return MigrationStub
     */
    public function getStub(): MigrationStub
    {
        return $this->stub;
    }

    /**
     * @param  MigrationStub  $stub
     * @return $this
     */
    public function setStub(MigrationStub $stub): self
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
        return $directory.'/'.$this->getDatePrefix().'_'.$name.'.php';
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
