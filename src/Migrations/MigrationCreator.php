<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Migrations;

use Illuminate\Filesystem\Filesystem;
use Alexeykhr\ClickhouseMigrations\StubFactory;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Alexeykhr\ClickhouseMigrations\Contracts\ClickhouseStubContract;

class MigrationCreator
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var ClickhouseStubContract
     */
    protected $stub;

    public function __construct(Filesystem $filesystem, ?ClickhouseStubContract $stub = null)
    {
        $this->filesystem = $filesystem;
        $this->stub = $stub ?? StubFactory::create();
    }

    /**
     * Create a new migration from stub file
     *
     * @param  string  $className
     * @param  string  $directory
     * @param  string|null  $table
     * @return string|null path to file
     * @throws FileNotFoundException
     */
    public function create(string $className, string $directory, ?string $table = null): ?string
    {
        $this->filesystem->ensureDirectoryExists($directory);

        $path = $this->generatePath($className, $directory);

        $content = $this->stub->generate($className, [
            'table' => $table,
        ]);

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
     * @return ClickhouseStubContract
     */
    public function getStub(): ClickhouseStubContract
    {
        return $this->stub;
    }

    /**
     * @param  ClickhouseStubContract  $stub
     * @return $this
     */
    public function setStub(ClickhouseStubContract $stub): self
    {
        $this->stub = $stub;

        return $this;
    }
}
