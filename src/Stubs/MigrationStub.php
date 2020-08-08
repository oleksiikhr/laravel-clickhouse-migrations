<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Stubs;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
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
     * @param  string  $filePath
     * @param  array  $parameters
     * @return string
     * @throws FileNotFoundException
     */
    public function generate(string $filePath, array $parameters = []): string
    {
        $content = $this->filesystem->get($filePath);

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
}
