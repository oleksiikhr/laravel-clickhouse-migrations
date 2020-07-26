<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Migrations;

use Illuminate\Filesystem\Filesystem;
use Alexeykhr\ClickhouseMigrations\StubFactory;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Alexeykhr\ClickhouseMigrations\Contracts\MigrationStubContract;

class MigrationCreator
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var MigrationStubContract
     */
    protected $stub;

    public function __construct(Filesystem $filesystem, ?MigrationStubContract $stub = null)
    {
        $this->filesystem = $filesystem;

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->stub = $stub ?? StubFactory::create();
    }

    /**
     * Create a new migration from stub file
     *
     * @param  string  $className
     * @param  string  $directory
     * @param  array  $parameters
     * @return string|null path to file
     * @throws FileNotFoundException
     */
    public function create(string $className, string $directory, array $parameters = []): ?string
    {
        $this->filesystem->ensureDirectoryExists($directory);

        $path = $this->generatePath($className, $directory);

        $content = $this->stub->generate($className, $parameters);

        return $this->filesystem->put($path, $content) === false ? null : $path;
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

    /**
     * @return MigrationStubContract
     */
    public function getStub(): MigrationStubContract
    {
        return $this->stub;
    }

    /**
     * @param  MigrationStubContract  $stub
     * @return $this
     */
    public function setStub(MigrationStubContract $stub): self
    {
        $this->stub = $stub;

        return $this;
    }
}
