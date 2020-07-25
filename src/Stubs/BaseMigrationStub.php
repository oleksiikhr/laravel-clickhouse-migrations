<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Stubs;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Alexeykhr\ClickhouseMigrations\Contracts\ClickhouseStubContract;

abstract class BaseMigrationStub implements ClickhouseStubContract
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
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
        $customPath = $this->getLaravelStubPath().'/'.$this->getFilename();

        if ($this->filesystem->exists($customPath)) {
            return $customPath;
        }

        return $this->getPackageStubPath().'/'.$this->getFilename();
    }

    /**
     * @return string|null
     * @throws FileNotFoundException
     */
    public function publish(): ?string
    {
        $this->filesystem->ensureDirectoryExists($this->getLaravelStubPath());

        $toPath = $this->getLaravelStubPath().'/'.$this->getFilename();

        $result = $this->filesystem->put(
            $toPath,
            $this->filesystem->get($this->getPackageStubPath().'/'.$this->getFilename())
        );

        return $result === false ? null : $toPath;
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

    /**
     * @return string
     */
    protected function getLaravelStubPath(): string
    {
        return base_path().'/stubs';
    }
}
