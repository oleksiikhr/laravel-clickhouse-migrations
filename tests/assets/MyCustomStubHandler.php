<?php declare(strict_types=1);

namespace Alexeykhr\ClickhouseMigrations\Tests\assets;

use Alexeykhr\ClickhouseMigrations\Contracts\MigrationStubHandlerContract;

class MyCustomStubHandler implements MigrationStubHandlerContract
{
    /**
     * @inheritDoc
     */
    public function populate(string $content, array $parameters): string
    {
        return str_replace('{{myCustomPlace}}', $parameters['myCustomParameter'], $content);
    }
}
