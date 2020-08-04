<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Stubs;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Alexeykhr\ClickhouseMigrations\Contracts\MigrationStubContract;

abstract class BaseMigrationStub implements MigrationStubContract
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Path to Stubs
     *
     * @var string
     */
    protected $path;

    public function __construct(Filesystem $filesystem, string $path)
    {
        $this->filesystem = $filesystem;
        $this->path = $path;
    }

    /**
     * @return string|null
     * @throws FileNotFoundException
     */
    public function publish(): ?string
    {
        $this->filesystem->ensureDirectoryExists($this->path);

        $result = $this->filesystem->put(
            $this->path.'/'.$this->getFilename(),
            $this->filesystem->get($this->getPackageStubPath().'/'.$this->getFilename())
        );

        return $result === false
            ? null
            : $this->path.'/'.$this->getFilename();
    }

    /**
     * @inheritDoc
     */
    public function generate(string $className, array $parameters = []): string
    {
        return $this->populateClass($className, $this->getStub());
    }

    /**
     * @return string
     * @throws FileNotFoundException
     */
    public function getStub(): string
    {
        $path = $this->getStubPath();

        return $this->filesystem->get($path);
    }

    /**
     * Get the path to the stubs
     *
     * @return string
     */
    public function getStubPath(): string
    {
        $customPath = $this->path.'/'.$this->getFilename();

        if ($this->filesystem->exists($customPath)) {
            return $customPath;
        }

        return $this->getPackageStubPath().'/'.$this->getFilename();
    }

    /**
     * Populate the place-holders in the migration stub
     *
     * @param  string  $className
     * @param  string  $content
     * @return string
     */
    protected function populateClass(string $className, string $content): string
    {
        return str_replace(
            ['DummyClass', '{{ class }}', '{{class}}'],
            $this->getClassName($className),
            $content
        );
    }

    /**
     * Get the class name of a migration name
     *
     * @param  string  $name
     * @return string
     */
    protected function getClassName($name): string
    {
        return Str::studly($name);
    }

    /**
     * @return string
     */
    protected function getPackageStubPath(): string
    {
        return __DIR__.'/../../stubs';
    }
}
