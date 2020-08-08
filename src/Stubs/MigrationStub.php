<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Stubs;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Alexeykhr\ClickhouseMigrations\Contracts\MigrationStubContract;
use Alexeykhr\ClickhouseMigrations\Contracts\MigrationStubHandlerContract;

class MigrationStub implements MigrationStubContract
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var MigrationStubHandlerContract[]
     */
    protected $handlers = [];

    public function __construct(Filesystem $filesystem, array $handlers = [])
    {
        $this->filesystem = $filesystem;
        $this->handlers = $handlers;
    }

    /**
     * @inheritDoc
     */
    public function generate(string $fileName, string $stubPath, array $parameters = []): string
    {
        $content = $this->filesystem->get($stubPath);

        // It is necessary to have the correct class to be able to do rollback
        $content = $this->populateClass($content, $fileName);

        foreach ($this->handlers as $handler) {
            $content = $handler->populate($content, $parameters);
        }

        return $content;
    }

    /**
     * @return MigrationStubHandlerContract[]
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * @param  MigrationStubHandlerContract[]  $handlers
     * @return $this
     */
    public function setHandlers(array $handlers): self
    {
        $this->handlers = $handlers;

        return $this;
    }

    /**
     * @param  string  $content
     * @param  string  $fileName
     * @return string
     */
    public function populateClass(string $content, string $fileName): string
    {
        return str_replace(
            ['DummyClass', '{{ class }}', '{{class}}'],
            $this->getClassName($fileName),
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
}
