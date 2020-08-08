<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Stubs\Handlers;

use Illuminate\Support\Str;
use Alexeykhr\ClickhouseMigrations\Contracts\MigrationStubHandlerContract;

class ClassStubHandler implements MigrationStubHandlerContract
{
    /**
     * @inheritDoc
     */
    public function populate(string $content, array $parameters): string
    {
        return str_replace(
            ['DummyClass', '{{ class }}', '{{class}}'],
            $this->getClassName($parameters['className']),
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
